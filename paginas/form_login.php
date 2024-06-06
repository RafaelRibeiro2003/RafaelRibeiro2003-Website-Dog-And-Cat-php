<!DOCTYPE html>
<html>

<head>
  <title>Login</title>
  <meta charset="utf-8" />

  <link rel="stylesheet" href="./style.css" />
  <link href="./home.css" rel="stylesheet" />
</head>
<?php
session_start();
include './constantes_funcoes.php';

$cargosAceites = array(VISITANTE);
jobProtection($cargosAceites);
$objetivosAceites = array('fazerLogin');
objectiveProtection($objetivosAceites);

?>

<body>
  <div id="container_form">
    <div class="logo_container">
      <a href='pag_inicial.php'><img src="logo.png" /></a>
    </div>
    <div class="formulario">
      <h1 id="form_title">
        <span>Login</span>
        <br />
      </h1>
      <form action="login.php?obj=loggingIn" method="post" id="form">
        <div class="input_container">
          <label>Utilizador:&nbsp;</label><input type="text" name="utilizador" class="input" required />
        </div>
        <div class="input_container">
          <label>Password:&nbsp;</label><input type="password" name="password" class="input" required />
        </div>
        <div class="button_container">
          <a href="pag_inicial.php" id="cancel_button"> <span>Cancelar</span> </a>
          <input type="submit" name="submit" value="Entrar" id="confirm_button" />
        </div>
      </form>
      <div id="regist_container">
        <span>Não tem conta?&nbsp;</span>
        <a href="form_registar.php?obj=criarConta"> <span>Registe-se...</span> </a>
      </div>
    </div>
  </div>
</body>

</html>