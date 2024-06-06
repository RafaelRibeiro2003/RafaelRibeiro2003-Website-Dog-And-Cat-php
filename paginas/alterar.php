<?php
session_start();
include './constantes_funcoes.php';

sessionProtection();
$cargosAceites = array(CLIENTE, FUNCIONARIO, ADMINISTRADOR);
jobProtection($cargosAceites);
$objetivosAceites = array('alterarDadosPessoais', 'editarDadosUser', 'alterarReserva', 'aceitarCliente');
objectiveProtection($objetivosAceites);


if ($_GET['obj'] == 'alterarReserva') {
	$loc = 'reservas.php?obj=gerirReservas';
	try {
	if (!isset($_POST['reserva'], $_POST["animal"], $_POST["cliente"], $_POST["funcionario"], $_POST["data_comb"], $_POST["hora_comb"], $_POST["marcacao"], $_POST["hora_concl"]))
		throw new Exception('Não foi possível marcar a reserva houve problemas na transmissão dos elementos.');

	$cliente = $_POST["cliente"];
	$funcionario = $_POST["funcionario"];
	$data_comb = $_POST["data_comb"];
	$hora_comb = $_POST["hora_comb"];
	$hora_concl = $_POST["hora_concl"];
	$marcacao = $_POST['marcacao'];
	$animal = $_POST['animal'];
	$id = $_POST['reserva'];

	include './constMarcacao.php';
	include '../basedados/basedados.h';
	
		$comp = $marcacao . "." . $animal;
		$sql = "SELECT * FROM utilizadores WHERE username ='$funcionario' AND competencia LIKE '%$comp%'";
		$retval = verificaConexao($conn, $sql);

		if (mysqli_num_rows($retval) == 0)
			throw new Exception("Não foi possível registar a reserva pois o funcionário $funcionario não tem as competências necessárias.");


		$sql = "SELECT id FROM reservas
		WHERE funcionario ='$funcionario' AND id != $id AND data_comb = '$data_comb'
		AND ( hora_comb BETWEEN '$hora_comb' AND $hora_concl
		OR hora_concl BETWEEN '$hora_comb' AND $hora_concl
		OR '$hora_comb' BETWEEN hora_comb AND hora_concl ) ";

		$retval = verificaConexao($conn, $sql);
		if (mysqli_num_rows($retval) > 0)
			throw new Exception("Não foi possível registar a reserva pois o funcionário " . $funcionario . " não tem as disponibilidade a essa hora.");


		$sql = "UPDATE reservas SET cliente='$cliente',funcionario='$funcionario',marcacao=$marcacao,
		animal='$animal',data_comb='$data_comb',hora_comb='$hora_comb',hora_concl=$hora_concl WHERE id =$id";
		$retval = verificaConexao($conn, $sql);

	} catch (EXCEPTION $e) {
		echo "<h2>" . $e->getMessage() . "</h2>";
		if (isset($conn))
			mysqli_close($conn);
		header("Refresh:4; url=$loc");
		exit();
	}

	echo '<h2>Reserva alterada com sucesso!!</h2>';
	mysqli_close($conn);
	header("Refresh:2; url=$loc");
	exit();
}

if ($_GET['obj'] == 'aceitarCliente') {
	try {
		$loc = 'gerir_users.php?obj=gerirUsers';
		if (!isset($_GET['username']))
			throw new Exception('Não definiu o utilizador que pretende aceitar como cliente, por favor tente outra vez!');

		include '../basedados/basedados.h';
		$sql = "UPDATE utilizadores SET cargo=" . CLIENTE . " WHERE username='" . $_GET['username'] . "'";
		$retval = verificaConexao($conn, $sql);
		if (mysqli_affected_rows($conn) == 0)
			throw new Exception('Nenhum visitante foi aceite, por favor tente outra vez!');

	} catch (Exception $e) {
		echo "<h2>" . $e->getMessage() . "</h2>";
		if (isset($conn))
			mysqli_close($conn);
		header("Refresh:3; url=$loc");
		exit();
	}

	echo "<h2>Visitante aceite com sucesso!</h2>";
	mysqli_close($conn);
	header("Refresh:2; url=$loc");
}



if ($_GET['obj'] == 'editarDadosUser')
	$loc = "dados_pessoais.php?obj=editarDadosUser&username=" . $_POST['username'];
else if ($_GET['obj'] == 'alterarDadosPessoais')
	$loc = "dados_pessoais.php?obj=alterarDadosPessoais";


if ($_GET['obj'] == 'editarDadosUser' || $_GET['obj'] == 'alterarDadosPessoais') {
	try {
		if (!isset($_POST['username'], $_POST["telemovel"], $_POST["imagem"], $_POST['password'], $_POST["cargo"], $_POST["old_cargo"]))
			throw new Exception('Não foi possível alterar os dados, os parâmetros estão inválidos ou o nome de utilizador já existe.');

		$user = $_POST["username"];
		$tel = $_POST["telemovel"];
		$img = $_POST["imagem"];
		$pass = $_POST['password'];
		$old_cargo = $_POST["old_cargo"];
		if ($pass != "")
			$pass = "pass='" . md5($pass) . "',";
		$cargo = $_POST["cargo"];
		$extra = "";

		include '../basedados/basedados.h';
		if ($cargo != $old_cargo) {
			if ($old_cargo == FUNCIONARIO || $old_cargo == ADMINISTRADOR) {
				$extra = ", competencia='' ";
				$sql = "DELETE FROM reservas WHERE funcionario='$user' AND (data_comb > CURRENT_DATE OR (data_comb = CURRENT_DATE AND hora_comb > CURRENT_TIME))";
				verificaConexao($conn, $sql);
			}
			if ($old_cargo == CLIENTE) {
				$sql = "DELETE FROM reservas WHERE cliente='$user' AND (data_comb > CURRENT_DATE OR (data_comb = CURRENT_DATE AND hora_comb > CURRENT_TIME))";
				verificaConexao($conn, $sql);
			}
		}

		include './constMarcacao.php';
		$sql = "UPDATE utilizadores SET imagem='$img', $pass telemovel='$tel', cargo=$cargo $extra WHERE username='$user'";
		verificaConexao($conn, $sql);

		if ($_GET['obj'] == 'alterarDadosPessoais') {
			$_SESSION["telemovel"] = $tel;
			$_SESSION["imagem"] = $img;
			$_SESSION['password'] = $pass;
			$_SESSION["cargo"] = $cargo;
		}
	} catch (EXCEPTION $e) {
		echo "<h2>" . $e->getMessage() . "</h2>";
		if (isset($conn))
			mysqli_close($conn);
		header("Refresh:3; url=$loc");
		exit();
	}

	echo '<h2>Dados da conta foram alterados com sucesso!!</h2>';
	mysqli_close($conn);
	header("Refresh:2; url=$loc");
}
?>