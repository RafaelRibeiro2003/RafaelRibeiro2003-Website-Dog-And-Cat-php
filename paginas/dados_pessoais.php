<!DOCTYPE html>

<head>
  <title>Dados Pessoais</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./style.css" />
  <link rel="stylesheet" href="./home.css" />
</head>
<?php
session_start();
include './constantes_funcoes.php';
include './constMarcacao.php';
include '../basedados/basedados.h';

sessionProtection();
$cargosAceites = array(CLIENTE, FUNCIONARIO, ADMINISTRADOR);
jobProtection($cargosAceites);
$objetivosAceites = array('alterarDadosPessoais', 'editarDadosUser');
objectiveProtection($objetivosAceites);
?>

<body>
  <div>
    <header id="header">
      <div class="logo_container">
        <a href='pag_inicial.php'><img src="logo.png" /></a>
      </div>
      <div class="nav">
        <a href='reservas.php?obj=gerirReservas' class='button'>Reservas</a>
        <?php
        if ($_GET['obj'] == 'editarDadosUser')
          echo " <a href='dados_pessoais.php?obj=alterarDadosPessoais' class='button'>Dados Pessoais</a>";
        if ($_SESSION['cargo'] == ADMINISTRADOR)
          echo " <a href='gerir_users.php?obj=gerirUsers' class='button'>Gerir Elementos</a>";
        ?>
        <a href="logout.php?obj=loggingOut" class="button">Logout</a>
      </div>
    </header>
    <div id="body_perfil">

      <?php

      if ($_GET['obj'] == 'editarDadosUser') {

        $sql = "SELECT * FROM utilizadores WHERE username ='" . $_GET['username'] . "'";

        $retval = verificaConexao($conn, $sql);
        $row = mysqli_fetch_array($retval);
        if ($row == null) {
          echo "<h2>O utilizador a editar não foi encontrado, tente outra vez!</h2>";
          mysqli_close($conn);
          header("Refresh:2; url=gerir_users.php?obj=gerirUsers;");
        }

        $user = $row['username'];
        $img = $row['imagem'];
        $telemovel = $row['telemovel'];
        $password = $row['pass'];
        $cargo = $row['cargo'];

        $loc = "dados_pessoais.php?obj=editarDadosUser&username=$user";
        $nome_titulo = "Dados do Utilizador";

      } else if ($_GET['obj'] == 'alterarDadosPessoais') {
        $user = $_SESSION['username'];
        $img = $_SESSION['imagem'];
        $telemovel = $_SESSION['telemovel'];
        $password = $_SESSION['password'];
        $cargo = $_SESSION['cargo'];

        $nome_titulo = 'Dados Pessoais';
        $loc = "dados_pessoais.php?obj=alterarDadosPessoais";
      }
      ?>
      <h1 class='perfil_title'><span>
          <?php echo $nome_titulo; ?>
        </span></h1>
      <div class='perfil_container'>
        <div class='container_imagem_perfil'><img src='<?php echo usarImagemCorreta($img); ?>' /></div>

        <form action='alterar.php?obj=<?php echo $_GET['obj']; ?>' method='post' class='dados_container'>
          <div class='input_container'>
            <label>Utilizador:&nbsp;</label><input type='text' name='username' class='input'
              value='<?php echo $user; ?>' readonly />
          </div>
          <div class='input_container'>
            <label>Imagem:&nbsp;</label><input type='text' name='imagem' class='input' value='<?php echo $img; ?>' />
          </div>
          <div class='input_container'>
            <label>Telemóvel:&nbsp;</label><input type='tel' pattern='[0-9]{9}' name='telemovel' class='input' required
              value='<?php echo $telemovel; ?>' />
          </div>
          <div class='input_container'>
            <label>Cargo:&nbsp;</label>
            <?php
            if ($_SESSION['cargo'] == ADMINISTRADOR) {
              echo "<select name='cargo' class='input'>";
              $cargos = array(VISITANTE, CLIENTE, FUNCIONARIO, ADMINISTRADOR);
              foreach ($cargos as $c) {
                if ($cargo == $c)
                  echo "<option value='$c' selected>" . getCargo($c) . "</option>";
                else
                  echo "<option value='$c'>" . getCargo($c) . "</option>";
              }
              echo "</select>";
            } else {
              echo "<input type='text' name='cargo' class='input' value='" . getCargo($cargo) . "' readonly/>"; //apresentação
              echo "<input type='text' name='cargo' class='input' value='$cargo' hidden/>"; //variável a enviar
            }
            ?>
            <input type='text' name='old_cargo' value='<?php echo $cargo; ?>' hidden>
          </div>
          <div class='input_container'>
            <label>Password:&nbsp;</label><input type='password' name='password' minlength='3' maxlength='9'
              class='input' value='' />
          </div>

          <div class='button_container'>
            <a href='<?php echo $loc; ?>' id='cancel_button'> <span>Reiniciar</span> </a>
            <input type='submit' name='submit' value='Alterar' id='confirm_button' />
          </div>
        </form>
      </div>
      <?php
      if ($cargo == FUNCIONARIO || $cargo == ADMINISTRADOR) {
        $sql = "SELECT * FROM utilizadores WHERE username ='$user'";
        $retval = verificaConexao($conn, $sql);
        $row = mysqli_fetch_assoc($retval);

        if ($row['competencia'] != null) {
          ?>
          <h2 class='perfil_title'> Competências Adquiridas </h2>
          <div class='input_container'>
            <table class='tabelaCompetencia'>
              <tr>
                <th>Marcação</th>
                <th>Animal</th>
                <?php
                if ($_SESSION['cargo'] == ADMINISTRADOR)
                  echo "<th>Apagar</th>";
                echo "</tr>";

                $comp = explode(",", $row['competencia']);

                for ($i = 0; $i <= count($comp) - 1; $i++) {
                  $mar_animal = explode(".", $comp[$i]);

                  echo "<tr>
                <td>" . getNome($mar_animal[0]) . "</td>
                <td>" . getAnimal($mar_animal[1]) . "</td>";
                  if ($_SESSION['cargo'] == ADMINISTRADOR)
                    echo "<td> <a href='eliminar.php?obj=apagarCompetencia&comp=" . $comp[$i] . "&competencias=" . $row['competencia'] . "&username=$user&loc=" . $_GET['obj'] . "'> <img src=\"apagar.png\" width=\"40\" height=\"40\"> </a></td>";
                  echo "</tr>";
                }
                ?>
            </table>
          </div>
          <?php

        }
        $marcacao = array(LAVAGEM, CORTE);
        $animal = array('cao', 'gato');
        $counter = 0;

        foreach ($marcacao as $m) {
          foreach ($animal as $a) {
            $str = $m . "." . $a;
            if (strpos($row['competencia'], $str) === false) {
              $counter++;
            }
          }
        }

        if ($_SESSION['cargo'] == ADMINISTRADOR && $counter > 0) {
          ?>
          <h2 class='perfil_title'> Competências a Adicionar </h2>
          <div class='input_container'>
            <table class='tabelaCompetencia'>
              <tr>
                <th>Marcação</th>
                <th>Animal</th>

                <th>Adicionar</th>
              </tr>
              <?php
              foreach ($marcacao as $m) {
                foreach ($animal as $a) {
                  $str = $m . "." . $a;

                  if (strpos($row['competencia'], $str) === false) {
                    echo "<tr>
                    <td>" . getnome($m) . "</td>
                    <td>" . getAnimal($a) . "</td>";
                    echo "
                      <td> <a href='registar.php?obj=registarCompetencia&comp=" . $str . "&username=" . $user . "&loc=" . $_GET['obj'] . "'> <img src=\"permitir.png\" width=\"40\" height=\"40\"> </a></td>
                    </tr>";
                  }
                }
              }
              echo "
              </table>
              </div>";
        }
      }

      ?>
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