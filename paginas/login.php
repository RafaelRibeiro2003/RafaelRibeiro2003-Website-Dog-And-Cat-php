<?php
session_start();
include './constantes_funcoes.php';

$cargosAceites = array(VISITANTE);
jobProtection($cargosAceites);
$objetivosAceites = array('loggingIn');
objectiveProtection($objetivosAceites);

$loc = 'form_login.php?obj=fazerLogin';
try {
	if (!isset($_POST['utilizador'], $_POST['password']))
		throw new Exception('Não definiu o utilizador ou a password de login, por favor tente outra vez!');

	$utilizador = $_POST["utilizador"];
	$password = $_POST["password"];

	include '../basedados/basedados.h';
	$sql = "SELECT * FROM utilizadores WHERE username = '$utilizador'";
	$retval = verificaConexao($conn, $sql);


	if (mysqli_num_rows($retval) == 0)
		throw new Exception('Conta não foi encontrada, por favor tente outra vez!');

	$res = mysqli_fetch_array($retval);
	if ($res['pass'] != md5($password))
		throw new Exception('A senha introduzida está incorreta, por favor tente outra vez!');

	if ($res['cargo'] != VISITANTE) {
		$_SESSION["username"] = $utilizador;
		$_SESSION["password"] = $password;
		$_SESSION["cargo"] = $res['cargo'];
		$_SESSION["telemovel"] = $res['telemovel'];
		$_SESSION["imagem"] = $res['imagem'];
		echo '<h2>Entrou na conta com sucesso.</h2>';
		mysqli_close($conn);
		header("Refresh:2; url= pag_inicial.php");
		exit();
	} else {
		echo '<h2>O administador não autorizou a sua conta. Espere que a sua conta seja autorizada.</h2>';
		mysqli_close($conn);
		header("Refresh:4; url= pag_inicial.php");
	}
} catch (Exception $e) {
	echo "<h2>" . $e->getMessage() . "</h2>";
	if (isset($conn))
		mysqli_close($conn);
	header("Refresh:3; url=$loc");
	exit();
}


?>