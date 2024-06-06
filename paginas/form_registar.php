<!DOCTYPE html>
<html>

<head>
  <title>Registar</title>
  <meta charset="utf-8" />

  <link rel="stylesheet" href="./style.css" />
  <link href="./home.css" rel="stylesheet" />
</head>
<?php
session_start();
include './constantes_funcoes.php';

$cargosAceites = array(VISITANTE, ADMINISTRADOR);
jobProtection($cargosAceites);
$objetivosAceites = array('criarConta');
objectiveProtection($objetivosAceites);
if ($_SESSION['cargo'] == ADMINISTRADOR)
  sessionProtection();

$cargo = $_SESSION['cargo'];
if ($cargo == ADMINISTRADOR)
  $loc = 'gerir_users.php?obj=gerirUsers';
else
  $loc = 'form_login.php?obj=fazerLogin';
?>

<body>
  <div id="container_form">
    <div class="logo_container">
      <a href='pag_inicial.php'><img src="logo.png" /></a>
    </div>
    <div class="formulario">
      <h1 id='form_title'>
      </h1>
      <form action='registar.php?obj=Registar' method='post' id='form'>

        <div class="input_container">
          <label>Utilizador:&nbsp;</label><input type="text" name="utilizador" class="input" required />
        </div>
        <div class="input_container">
          <label>Imagem:&nbsp;</label><input type="text" name="imagem" class="input" />
        </div>
        <div class="input_container">
          <label>Telemóvel:&nbsp;</label><input type="tel" pattern="[0-9]{9}" minlength=9 maxlength=9 name="telemovel"
            class="input" required />
        </div>
        <div class="input_container">
          <label>Password:&nbsp;</label><input type="password" name="password" minlength='3' maxlength='9' class="input"
            required />
        </div>
        <div class="input_container">
          <label>Confirmação:&nbsp;</label><input type="password" name="confirm" minlength='3' maxlength='9'
            class="input" required />
        </div>
        <?php
        if ($cargo != ADMINISTRADOR)
          echo "<input type='text' name='cargo' class='input' value=" . VISITANTE . " hidden />";
        else {
          ?>

          <div class='input_container'>
            <label>Cargo:&nbsp;</label>
            <select name='cargo' class='input'>
              <option value='<?php echo VISITANTE; ?>' selected>Visitante</option>
              <option value='<?php echo CLIENTE; ?>'>Cliente</option>
              <option value='<?php echo FUNCIONARIO; ?>'>Funcionário</option>
              <option value='<?php echo ADMINISTRADOR; ?>'>Administrador</option>
            </select>
          </div>
          <?php
        }
        ?>
        <div class='button_container'>
          <a href='<?php echo $loc; ?>' id='cancel_button'> <span>Cancelar</span> </a>
          <input type='submit' name='submit' value='Registar' id='confirm_button' />
        </div>
      </form>
      <div id="regist_container">
      </div>
    </div>
  </div>
</body>

</html>