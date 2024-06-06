<?php
define("VISITANTE", 1);
define("CLIENTE", 2);
define("FUNCIONARIO", 3);
define("ADMINISTRADOR", 4);

define("RESERVA_AUTORIZADA", 1);
define("RESERVA_CONCLUIDA", 2);


function getCargo(int $num)
{
	switch ($num) {
		case VISITANTE:
			return 'Visitante';
		case CLIENTE:
			return 'Cliente';
		case FUNCIONARIO:
			return 'Funcionário';
		case ADMINISTRADOR:
			return 'Administrador';
		default:
			return 'Desconhecido';
	}
}

function usarImagemCorreta($imagem)
{
	if (!file_exists('' . $imagem) || $imagem == '')
		return "default.png";

	return $imagem;
}

function verificaConexao($conn, $sql)
{
	$retval = mysqli_query($conn, $sql);
	if (!$retval) {
		mysqli_close($conn);
		die('Could not connect: ' . mysqli_error($conn));
	}

	return $retval;
}

//proteção contra a entrada de utilizadores sem todas as informações de sessão (adquiridas no login) criadas
function sessionProtection()
{
	if (isset($_SESSION["username"], $_SESSION["password"], $_SESSION["cargo"], $_SESSION["telemovel"], $_SESSION["imagem"]))
		return;

	echo "<h3>Entrou sem as informações de sessão corretas.<br>Por favor, pare de quebrar o nosso sistema :(</h3>";
	header('Refresh:4; url=logout.php?obj=loggingOut');
	exit();

}

//proteção contra a entrada de utilizadores com cargos indesejados no php
function jobProtection($cargos)
{
	if (isset($_SESSION['cargo']) && in_array($_SESSION['cargo'], $cargos))
		return;

	echo "<h3>Entrou numa zona restrita do nosso website, se continuar com estas ações teremos que tomar medidas drásticas }:-)</h3>";
	header('Refresh:4; url=pag_inicial.php');
	exit();

}

//proteção para php's que fazem a separação de objetivos a concretizar no mesmo
function objectiveProtection($objetivos)
{
	if (isset($_GET['obj']) && in_array($_GET['obj'], $objetivos))
		return;

	echo "<h3>O seu objetivo não é autorizado nesta página, se continuar com estas ações teremos que tomar medidas drásticas }:-)</h3>";
	header('Refresh:4; url=pag_inicial.php');
	exit();
}

?>