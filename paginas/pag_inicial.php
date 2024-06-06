<!DOCTYPE html>
<html>

<head>
  <title>Home Page</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./style.css" />
  <link href="./home.css" rel="stylesheet" />
</head>
<?php
session_start();
include './constantes_funcoes.php';
include './constMarcacao.php';

if (!isset($_SESSION['cargo']))
  $_SESSION['cargo'] = VISITANTE;

$cargosAceites = array(VISITANTE, CLIENTE, FUNCIONARIO, ADMINISTRADOR);
jobProtection($cargosAceites);

if ($_SESSION['cargo'] != VISITANTE)
  sessionProtection();

?>

<body>

  <div>
    <header id="header">
      <div class="logo_container">
        <img src="logo.png" />
      </div>
      <div class="nav">
        <!--Acrescentar mais botões aqui-->
        <?php
        if ($_SESSION['cargo'] == VISITANTE)
          echo " <a href='form_login.php?obj=fazerLogin' class='button'>Login/Registar</a>";
        else {
          ?>
          <a href='dados_pessoais.php?obj=alterarDadosPessoais' class='button'>Dados Pessoais</a>
          <a href='reservas.php?obj=gerirReservas' class='button'>Reservas</a>
          <?php
          if ($_SESSION['cargo'] == ADMINISTRADOR)
            echo " <a href='gerir_users.php?obj=gerirUsers' class='button'>Gerir Elementos</a>";
          ?>
          <a href='logout.php?obj=loggingOut' class='button'>Logout</a>
          <?php
        }

        ?>
      </div>
    </header>
    <div id="body">
      <div class="seccao_right">
        <div class="image_complement">
          <img class="right" src="dogAndCat.jpg" />
        </div>
        <div class="info_box_right">
          <h1 class="title_right">
            <span>Bem-vindo</span>
            <br />
          </h1>
          <span class="text_right">
            <span>
              Preparamos aqui os melhores serviços para todas as idades e
              com todas as regalias incluídas, apenas para cães e gatos.
              Garantimos que o seu animal de estimação sentirá o conforto
              duma estadia no nosso salão de beleza.
            </span>
            <br />
            <span>
              A segurança do seu companheiro também será a nossa prioridade
              nº 1. Afinal de contas, este descanso é tanto seu como será do
              seu animal.
            </span>
            <br />
            <br />
          </span>
        </div>
      </div>

      <div class="seccao_left">
        <div class="info_box_left">
          <h1 class="title_left">
            <span>Autores</span>
            <br />
          </h1>
          <span class="text_left">
            <dl>
              <br>
              <dt>
                <li>Ricardo Campos Pinho</li>
              </dt>
              <dd>&emsp;&emsp;Idade: 19 anos</dd>
              <dd>&emsp;&emsp;Emprego: estudante</dd>
              <dd>&emsp;&emsp;Localidade: Castelo Branco</dd>
              <dd>&emsp;&emsp;Contato: 9********</dd>
              <br>
              <dt>
                <li>Rafael Coelho Ribeiro</li>
              </dt>
              <dd>&emsp;&emsp;Idade: 19 anos</dd>
              <dd>&emsp;&emsp;Emprego: estudante</dd>
              <dd>&emsp;&emsp;Localidade: Leiria</dd>
              <dd>&emsp;&emsp;Contato: 9********</dd>
            </dl>
          </span>
        </div>
        <div class="image_complement">
          <img class="middle" src="rafael.JPG" />
          <img class="left" src="Ricardo.jpeg" />
        </div>
      </div>

      <div class='reservas'>
        <h1>Serviços</h1>
        <div class='seccao_reservas'>
          <div>
            <?php
            echo "
            <img src=\"lavagem.jpg\">
            <span class='marcacao_titulo'>" . getNome(LAVAGEM) . "</span>
              <span class='marcacao_custo'>" . getPreco(LAVAGEM) . "€</span>
              <span>Duração: " . getDuracao(LAVAGEM) . "min <br><br>
              " . getDescricao(LAVAGEM) . " <br><br>
              </span>";
            ?>
          </div>
          <div>
            <?php
            echo "
            <img src=\"corte.jpg\">
            <span class='marcacao_titulo'>" . getNome(CORTE) . "</span>
            <span class='marcacao_custo'>" . getPreco(CORTE) . "€</span>
            <span>Duração: " . getDuracao(CORTE) . "min <br><br>
            " . getDescricao(CORTE) . " <br><br>
            </span>";
            ?>
          </div>
        </div>
      </div>

      <div class='horario'>
        <div>
          <h1>Horário de funcionamento</h1><br>
          Todos os Dias - 9:00 até ás 18:00 <br><br>

          <b>Endereço:</b><br>
          Av. Pedro Álvares Cabral, nº 12<br>
          6000-084 Castelo Branco

        </div>
      </div>
    </div>

    <footer id="footer">
      <div class="logo_container">
        <img src="logo.png" />
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