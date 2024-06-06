<!DOCTYPE html>

<head>
  <title>Dados da Reserva</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./style.css" />
  <link rel="stylesheet" href="./home.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<?php
session_start();
include './constantes_funcoes.php';
include './constMarcacao.php';

sessionProtection();
$cargosAceites = array(ADMINISTRADOR, CLIENTE, FUNCIONARIO);
jobProtection($cargosAceites);
$objetivosAceites = array('editarMarcacao', 'editarReserva');
objectiveProtection($objetivosAceites);
$cargo = $_SESSION['cargo'];
?>

<body>
  <div>
    <header id="header">
      <div class="logo_container">
        <a href='pag_inicial.php'><img src="logo.png" /></a>
      </div>
      <div class="nav">
        <a href='dados_pessoais.php?obj=alterarDadosPessoais' class='button'>Dados Pessoais</a>
        <?php
        if ($cargo == ADMINISTRADOR)
          echo " <a href='gerir_users.php?obj=gerirUsers' class='button'>Gerir Elementos</a>";
        ?>
        <a href='logout.php?obj=loggingOut' class='button'>Logout</a>
      </div>
    </header>
    <div id="body_perfil">

      <h1 class="perfil_title"><span>Dados da Reserva</span></h1>
      <?php
      include '../basedados/basedados.h';

      $obj = $_GET['obj'];
      if ($obj == 'editarMarcacao')
        $action = 'editar_reserva.php?obj=editarReserva';
      else
        if ($obj == 'editarReserva')
          $action = 'alterar.php?obj=alterarReserva';

      if ($obj == 'editarMarcacao') {
        try {
          if (!isset($_GET['reserva'], $_GET['estado']))
            throw new Exception('Existem dados em falta.');

          $estado = $_GET['estado'];

          $sql = "SELECT * FROM reservas WHERE id ='" . $_GET['reserva'] . "'";
          $retval = verificaConexao($conn, $sql);
          $row = mysqli_fetch_array($retval);
          if ($row == null)
            throw new Exception('A reserva que escolheu já não existe.');

        } catch (Exception $e) {
          echo "<h2>" . $e->getMessage() . "</h2>";
          if (isset($conn))
            mysqli_close($conn);
          header("Refresh:3; url=reservas.php?obj=gerirReservas");
          exit();
        }

        $cliente = $row['cliente'];
        $funcionario = $row['funcionario'];
        $marcacao = $row['marcacao'];
        $animal = $row['animal'];
        $data_comb = $row['data_comb'];
        $hora_comb = $row['hora_comb'];

      } else
        if ($obj == 'editarReserva') {
          $reserva = $_POST['reserva'];
          $cliente = $_POST['cliente'];
          $funcionario = $_POST['funcionario'];
          $marcacao = $_POST['marcacao'];
          $animal = $_POST['animal'];
          $data_comb = $_POST['data_comb'];
          $hora_comb = $_POST['hora_comb'];
        }
      ?>

      <div class='perfil_container'>
        <form action='<?php echo $action; ?>' method='post' class='dados_container'>

          <?php
          if ($obj == 'editarMarcacao') {

            ?>
            <!--Select da Marcação-->
            <div class='input_container'>
              <label>Marcação:&nbsp;</label>
              <select name='marcacao' class='input'>

                <?php
                echo "<option value=$marcacao selected>" . getNome($marcacao) . "</option>"; //marcação escolhida
                switch ($marcacao) {
                  case LAVAGEM:
                    echo "<option value=" . CORTE . ">" . getNome(CORTE) . "</option>";
                    break;
                  case CORTE:
                    echo "<option value=" . LAVAGEM . ">" . getNome(LAVAGEM) . "</option>";
                }
                ?>
              </select>
            </div>
            <!--Select do Animal-->
            <div class='input_container'>
              <label>Animal:&nbsp;</label>
              <select name='animal' class='input'>
                <?php
                echo "<option value=$animal selected>" . getAnimal($animal) . "</option>"; //marcação escolhida
                switch ($animal) {
                  case CAO:
                    echo "<option value=" . GATO . ">" . getAnimal(GATO) . "</option>";
                    break;
                  case GATO:
                    echo "<option value=" . CAO . ">" . getAnimal(CAO) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class='container_tempo'>
              <label> Data:&nbsp;</label>
              <?php

              if ($estado == 'inacabada')
                echo "<input value='$data_comb' type='date' name='data_comb' min='" . date_format(date_create(date('d-m-Y', time())), 'Y-m-d') . "' class='input' />";
              else
                if ($estado == 'concluida')
                  echo "<input value='$data_comb' type='date' name='data_comb' class='input' />";
              ?>
              <label> Hora:&nbsp;</label>
              <input type='time' value='<?php echo $hora_comb; ?>' step=1800 min='09:00:00' max='18:00:00'
                name='hora_comb' class='input' />
            </div>
            <input type='hidden' value='<?php echo $cliente; ?>' name='cliente'>
            <input type='hidden' value='<?php echo $funcionario; ?>' name='funcionario'>
            <input type='hidden' value='<?php echo $_GET['reserva'] ?>' name='reserva'>
            <?php
          } else
            if ($obj == 'editarReserva') {
              $horas = explode(':', $hora_comb);

              $duracao = getDuracao($marcacao);

              $horas[1] = (int) $horas[1] + $duracao;
              $hora_concl = ((int) $horas[0] + (int) ($horas[1] / 60)) * 10000 + $horas[1] % 60 * 100;
              ?>
              <input type='hidden' value='<?php echo $animal; ?>' name='animal'>
              <input type='hidden' value='<?php echo $marcacao; ?>' name='marcacao'>
              <input type='hidden' value='<?php echo $data_comb; ?>' name='data_comb'>
              <input type='hidden' value='<?php echo $hora_comb; ?>' name='hora_comb'>

              <input type='hidden' value='<?php echo $hora_concl; ?>' name='hora_concl'>
              <input type='hidden' value='<?php echo $_POST['reserva']; ?>' name='reserva'>
              <!--Select do Cliente-->
              <?php
              if ($cargo != CLIENTE) {
                ?>
                <div class='input_container'>
                  <label>Cliente:&nbsp;</label>
                  <select name='cliente' class='input' required>
                    <?php
                    $sql = 'SELECT * FROM utilizadores WHERE cargo =' . CLIENTE;
                    $retval = verificaConexao($conn, $sql);

                    if (mysqli_num_rows($retval) > 0) {
                      while ($row = mysqli_fetch_assoc($retval)) {
                        if ($cliente == $row['username'])
                          echo "<option value='" . $row['username'] . "' selected>" . $row['username'] . "</option>";
                        else
                          echo "<option value='" . $row['username'] . "'>" . $row['username'] . "</option>";
                      }
                    }
                    ?>
                  </select>
                </div>
                <?php
              } else
                echo "<input type='hidden' value='$cliente' name='cliente'>";
              ?>
              <div class='input_container'>
                <label>Funcionário:&nbsp;</label>
                <select name='funcionario' class='input' required>
                  <?php
                  $comp = $marcacao . "." . $animal;
                  $horas = explode(':', $hora_comb);

                  $duracao = getDuracao($marcacao);

                  $horas[1] = (int) $horas[1] + $duracao;
                  $hora_fin = ((int) $horas[0] + (int) ($horas[1] / 60)) * 10000 + $horas[1] % 60 * 100;
                  $sql = "SELECT * FROM utilizadores WHERE cargo NOT IN (" . CLIENTE . "," . VISITANTE . ") 
                  AND competencia LIKE '%$comp%' AND username NOT IN 
                  (SELECT funcionario FROM reservas WHERE id != $reserva
                  AND data_comb = '$data_comb'
                  AND ( hora_comb BETWEEN '$hora_comb' AND $hora_fin
                  OR hora_concl BETWEEN '$hora_comb' AND $hora_fin
                  OR '$hora_comb' BETWEEN hora_comb AND hora_concl ))";

                  $retval = verificaConexao($conn, $sql);

                  if (mysqli_num_rows($retval) > 0) {
                    while ($row = mysqli_fetch_assoc($retval)) {
                      if ($funcionario == $row['username'])
                        echo "<option value='" . $row['username'] . "' selected>" . $row['username'] . "</option>";
                      else
                        echo "<option value='" . $row['username'] . "'>" . $row['username'] . "</option>";
                    }
                  } else
                    echo "<option value='' name='funcionario' disabled selected>Não existe um funcionario disponível</option>";
                  ?>
                </select>
              </div>

              <?php
            }
          mysqli_close($conn);
          ?>
          <div class='button_container'>
            <a href='reservas.php?obj=gerirReservas' id='cancel_button'> <span>Cancelar</span> </a>
            <input type='submit' name='submit' value='Alterar' id='confirm_button' />
          </div>
        </form>
      </div>
    </div>
  </div>
  <footer id="footer">
    <div class="logo_container">
      <a href='pag_inicial.php'><img src="logo.png" /></a>
    </div>
    <span class="text">
      © 2023 Dog&amp;CatSallon, All Rights Reserved.
    </span>
    <div class="icon_group">
      <img src="icon_twitter.png" />
      <img src="icon_facebook.png" />
      <img src="icon_instagram.png" />
    </div>
  </footer>

  </div>

</body>

</html>