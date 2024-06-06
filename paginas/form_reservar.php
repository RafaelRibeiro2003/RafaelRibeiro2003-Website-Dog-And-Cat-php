<!DOCTYPE html>

<head>
  <title>FormReserva</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./style.css" />
  <link href="./home.css" rel="stylesheet" />
</head>
<?php
session_start();
include './constantes_funcoes.php';

sessionProtection();
$cargosAceites = array(CLIENTE, FUNCIONARIO, ADMINISTRADOR);
jobProtection($cargosAceites);
$objetivosAceites = array('fazerReserva');
objectiveProtection($objetivosAceites);
?>

<body>

  <div id="container_form">
    <div class="logo_container">
      <a href='pag_inicial.php'><img src="logo.png" /></a>
    </div>

    <div class="formulario">
      <h1 id="form_title">
        <span>Marcação de Reserva</span>
        <br />
      </h1>

      <?php
      include './constMarcacao.php';
      include '../basedados/basedados.h';

      $cargo = $_SESSION['cargo'];
      $marcacao = $_POST['marcacao'];
      $animal = $_POST['animal'];
      $hora_comb = $_POST['hora'];
      $data_comb = $_POST['data'];

      $comp = $marcacao . "." . $animal;
      $horas = explode(':', $hora_comb);

      $duracao = getDuracao($marcacao);

      $horas[1] = (int) $horas[1] + $duracao;
      $hora_concl = ((int) $horas[0] + (int) ($horas[1] / 60)) * 10000 + $horas[1] % 60 * 100;
      $sql = "SELECT * FROM utilizadores WHERE cargo IN (" . FUNCIONARIO . "," . ADMINISTRADOR . ") 
                  AND competencia LIKE '%$comp%' AND username NOT IN 
                  (SELECT funcionario FROM reservas WHERE data_comb = '$data_comb'
                  AND ( hora_comb BETWEEN '$hora_comb' AND $hora_concl
                  OR hora_concl BETWEEN '$hora_comb' AND $hora_concl
                  OR '$hora_comb' BETWEEN hora_comb AND hora_concl ))";

      $retval = verificaConexao($conn, $sql);
      ?>

      <form action='registar.php?obj=registarReserva' method='post' id='form'>
        <div class='container_marcacoes'>
          <?php
          switch ($marcacao) {
            case LAVAGEM:
              echo "
                <div class='container_marcacao'>
                  <span class='marcacao_titulo'>" . getNome(LAVAGEM) . "</span>
                  <span class='marcacao_custo'>" . getPreco(LAVAGEM) . "€</span>
                  <span>Duração: " . getDuracao(LAVAGEM) . "min <br><br>
                  " . getDescricao(LAVAGEM) . " <br><br>
                  </span>
                </div>
              ";
              break;
            case CORTE:
              echo "
                <div class='container_marcacao'>
                  <span class='marcacao_titulo'>" . getNome(CORTE) . "</span>
                  <span class='marcacao_custo'>" . getPreco(CORTE) . "€</span>
                  <span>Duração: " . getDuracao(CORTE) . "min <br><br>
                  " . getDescricao(CORTE) . " <br><br>
                  </span>
                </div>
              ";
              break;
          }
          ?>

        </div>

        <div class='input_container'>
          <label>Funcionário:&nbsp;</label>
          <select name='funcionario' class='input' required>
            <?php

            if (mysqli_num_rows($retval) == 0)
              echo "<option name='funcionario' value='' selected disabled> Não existe funcionários com as competências ou disponibilidade definida!</option>";
            else
              while ($row = mysqli_fetch_array($retval))
                echo "<option name='funcionario' value='" . $row['username'] . "' selected>" . $row['username'] . "</option>";
            ?>

          </select>
        </div>
        <?php
        if ($cargo != CLIENTE) {
          ?>
          <div class='input_container'>
            <label>Cliente:&nbsp;</label>
            <select name='cliente' class='input' required>
              <?php
              $sql = 'SELECT * FROM utilizadores WHERE cargo =' . CLIENTE;
              $retval = verificaConexao($conn, $sql);
              if (mysqli_num_rows($retval) == 0)
                echo "<option value='' name='cliente' selected disabled> Não existem clientes!</option>";
              else
                while ($row = mysqli_fetch_array($retval))
                  echo "<option name='cliente' value='" . $row['username'] . "' selected>" . $row['username'] . "</option>";

              ?>
            </select>
          </div>
          <?php
        } else
          echo "<input type='hidden' name='cliente' value='" . $_SESSION['username'] . "'>";
        ?>
        <input type='hidden' name='data_comb' value='<?php echo $data_comb; ?>'>
        <input type='hidden' name='hora_comb' value='<?php echo $hora_comb; ?>'>
        <input type='hidden' name='hora_concl' value='<?php echo $hora_concl; ?>'>
        <input type='hidden' name='marcacao' value='<?php echo $marcacao; ?>'>
        <input type='hidden' name='animal' value='<?php echo $animal; ?>'>

        <div class='button_container'>
          <a href='reservas.php?obj=gerirReservas' id='cancel_button'> <span>Cancelar</span> </a>
          <input type='submit' name='submit' value='Marcar' id='confirm_button' />
        </div>
      </form>

    </div>
  </div>
</body>

</html>