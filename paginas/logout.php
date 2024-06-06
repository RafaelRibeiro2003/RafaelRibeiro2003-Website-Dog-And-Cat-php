<?php
session_start();
include './constantes_funcoes.php';


$objetivosAceites = array('loggingOut');
objectiveProtection($objetivosAceites);

session_unset();
session_destroy();
echo '<h2>Saiu da sua conta com sucesso.</h2>';
header("Refresh:2; url= pag_inicial.php");
?>