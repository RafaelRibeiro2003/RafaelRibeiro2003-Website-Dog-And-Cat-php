<?php
session_start();
include './constantes_funcoes.php';

$cargosAceites = array(VISITANTE, FUNCIONARIO, ADMINISTRADOR, CLIENTE);
jobProtection($cargosAceites);
$objetivosAceites = array('Registar', 'registarReserva', 'registarCompetencia');
objectiveProtection($objetivosAceites);
if ($_SESSION['cargo'] == ADMINISTRADOR)
	sessionProtection();

if ($_GET['obj'] == 'registarCompetencia') {
	$loc = 'pag_inicial.php';
	try {
		if (!isset($_GET["comp"], $_GET['username'], $_GET['loc']))
			throw new Exception('Não foi possível registar a nova competência, houve problemas na transmissão dos elementos!');

		$loc = $_GET['loc'];
		$user = $_GET['username'];
		if ($loc == "editarDadosUser")
			$loc = "dados_pessoais.php?obj=$loc&username=$user";
		else if ($loc == "alterarDadosPessoais")
			$loc = "dados_pessoais.php?obj=$loc";

		include './constMarcacao.php';
		include '../basedados/basedados.h';

		$sql = "SELECT competencia FROM utilizadores WHERE  username ='" . $_GET['username'] . "'";
		$retval = verificaConexao($conn, $sql);
		if (mysqli_num_rows($retval) == 0)
			throw new Exception('O utilizador não existe, por favor tente outra vez.');

		$competencias = mysqli_fetch_array($retval);

		$comp = $_GET["comp"];
		$competencias = $competencias[0];

		if (strcmp($competencias, "") == 0)
			$competencias = $comp;
		else
			$competencias = $competencias . ',' . $comp;


		$sql = "UPDATE utilizadores SET competencia = '" . $competencias . "' WHERE username = '" . $_GET['username'] . "'";
		verificaConexao($conn, $sql);
		if (mysqli_affected_rows($conn) == 0)
			throw new Exception('Nenhum utilizador foi alterado, por favor tente outra vez!');

	} catch (Exception $e) {
		echo "<h2>" . $e->getMessage() . "</h2>";
		if (isset($conn))
			mysqli_close($conn);
		header("Refresh:3; url=$loc");
		exit();
	}

	mysqli_close($conn);
	echo "<h2>A competência foi adicionada com sucesso.</h2>";
	header("Refresh:2; url=$loc");
	exit();
}


if ($_GET['obj'] == 'registarReserva') {
	$loc = 'reservas.php?obj=gerirReservas';
	try {
		if (!isset($_POST["animal"], $_POST["cliente"], $_POST["funcionario"], $_POST["data_comb"], $_POST["hora_comb"], $_POST["marcacao"], $_POST["hora_concl"]))
			throw new Exception('Não foi possível marcar a reserva houve problemas na transmissão dos elementos!');

		$cliente = $_POST["cliente"];
		$funcionario = $_POST["funcionario"];
		$data_comb = $_POST["data_comb"];
		$hora_comb = $_POST["hora_comb"];
		$hora_concl = $_POST["hora_concl"];
		$marcacao = $_POST['marcacao'];
		$animal = $_POST['animal'];

		include './constMarcacao.php';
		include '../basedados/basedados.h';
		$comp = $marcacao . "." . $animal;
		$sql = "SELECT * FROM utilizadores WHERE username ='" . $funcionario . "' AND competencia LIKE '%" . $comp . "%'";
		$retval = verificaConexao($conn, $sql);

		if (mysqli_num_rows($retval) == 0)
			throw new Exception("O funcionário '$funcionario' não existe ou não tem as competência '" . getNome($marcacao) . "' para '" . getAnimal($animal) . "'!");

		//Determina se o funcionário já tem uma reserva nessa data, entre essas horas
		$sql = "SELECT id FROM reservas
        WHERE funcionario ='$funcionario' AND data_comb = '$data_comb'
        AND ( hora_comb BETWEEN '$hora_comb' AND $hora_concl
        OR hora_concl BETWEEN '$hora_comb' AND $hora_concl
        OR '$hora_comb' BETWEEN hora_comb AND hora_concl ) ";
		$retval = verificaConexao($conn, $sql);

		if (mysqli_num_rows($retval) > 0)
			throw new Exception("O funcionário '$funcionario' não existe ou não tem as disponibilidade às '$hora_comb' de '$data_comb'!");


		$sql = "INSERT INTO reservas VALUES (null, '$cliente', '$funcionario', $marcacao,'$animal','$data_comb','$hora_comb',$hora_concl)";
		verificaConexao($conn, $sql);
		if (mysqli_affected_rows($conn) == 0)
			throw new Exception('Não foi possível fazer a marcação os parâmetros são inválidos ou não foram transmitidos corretamente!');

	} catch (Exception $e) {
		echo "<h2>" . $e->getMessage() . "</h2>";
		if (isset($conn))
			mysqli_close($conn);
		header("Refresh:3; url=$loc");
		exit();
	}

	echo '<h2>Reserva marcada com sucesso!!</h2>';
	mysqli_close($conn);
	header("Refresh:2; url=$loc");
	exit();
}

if ($_GET['obj'] == 'Registar') {
	$loc = 'form_registar.php?obj=criarConta';
	try {
		if (!isset($_POST["utilizador"], $_POST["imagem"], $_POST["password"], $_POST["telemovel"], $_POST["cargo"], $_POST["confirm"]))
			throw new Exception('Não foi possível registar houve problemas na transmissão dos elementos!');

		$pass = $_POST["password"];
		$user = $_POST["utilizador"];
		$img = $_POST["imagem"];
		$cargo = $_POST["cargo"];
		$telemovel = $_POST["telemovel"];

		if ($pass != $_POST["confirm"])
			throw new EXCEPTION('As duas passwords são diferentes, por favor tente outra vez!');

		include '../basedados/basedados.h';
		$sql = "SELECT * FROM utilizadores WHERE username = '$user'";
		$retval = verificaConexao($conn, $sql);
		if (mysqli_num_rows($retval) > 0)
			throw new Exception("Não foi possível registar o utilizador '$user' já existe!");

		$sql = "INSERT INTO utilizadores VALUES ('$user', '$img ', '" . md5($pass) . "', $telemovel, $cargo, '')";
		verificaConexao($conn, $sql);
		if (mysqli_affected_rows($conn) == 0)
			throw new Exception('Não foi possível registar os parâmetros são inválidos ou o utilizador já existe.');

	} catch (Exception $e) {
		echo "<h2>" . $e->getMessage() . "</h2>";
		if (isset($conn))
			mysqli_close($conn);
		header("Refresh:3; url=$loc");
		exit();
	}

	if ($_SESSION['cargo'] == VISITANTE)
		$loc = 'pag_inicial.php';
	else if ($_SESSION['cargo'] == ADMINISTRADOR)
		$loc = 'gerir_users.php?obj=gerirUsers';

	echo '<h2>Conta criada com sucesso!!</h2>';
	mysqli_close($conn);
	header("Refresh:2; url=$loc");
}
?>