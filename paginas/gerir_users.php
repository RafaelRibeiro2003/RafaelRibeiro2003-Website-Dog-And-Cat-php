<html>

<head>
	<meta charset='UTF-8'>
	<title>Gestão de Utilizadores</title>
	<link rel='stylesheet' href='style.css'>
	<link rel='stylesheet' href='home.css'>
</head>
<?php
session_start();
include './constantes_funcoes.php';

sessionProtection();
$cargosAceites = array(ADMINISTRADOR);
jobProtection($cargosAceites);
$objetivosAceites = array('gerirUsers');
objectiveProtection($objetivosAceites);
?>

<body>
	<div>
		<header id="header">
			<div class="logo_container">
				<a href='pag_inicial.php'><img src="logo.png" /></a>
			</div>
			<div class="nav">
				<a href='dados_pessoais.php?obj=alterarDadosPessoais' class='button'>Dados Pessoais</a>
				<a href='reservas.php?obj=gerirReservas' class='button'>Reservas</a>
				<a href='logout.php?obj=loggingOut' class='button'>Logout</a>
			</div>
		</header>

		<div id='body_tables'>
			<a class='button' href='form_registar.php?obj=criarConta'>Criar Conta</a></br></br>

			<?php
			include '../basedados/basedados.h';
			/*Tabela de Funcionários e admins (excluindo o próprio admin da sessão)*/
			$sql = "SELECT * FROM utilizadores WHERE cargo IN (" . FUNCIONARIO . "," . ADMINISTRADOR . ") AND username !='" . $_SESSION['username'] . "'";
			$retval = verificaConexao($conn, $sql);

			if (mysqli_num_rows($retval) > 0) {
				showTableHeader('Funcionários');
				echo "<th>Despedir</th></tr>";

				while ($row = mysqli_fetch_array($retval)) {
					showInfoPessoal($row);
					echo "
				<td> <a href='eliminar.php?obj=eliminarUser&username=" . $row['username'] . "'> <img src=\"apagar.png\" width=\"40\" height=\"40\"> </a></td>
			</tr>
			";
				}
			}

			/*Tabela de visitantes por autorizar*/
			$sql = 'SELECT * FROM utilizadores WHERE cargo =' . VISITANTE;
			$retval = verificaConexao($conn, $sql);

			if (mysqli_num_rows($retval) > 0) {
				showTableHeader('Visitantes por Autorizar');

				echo "<th>Permitir</th><th>Recusar</th> </tr>";

				while ($row = mysqli_fetch_assoc($retval)) {
					showInfoPessoal($row);
					echo "
				<td> <a href='alterar.php?username=" . $row['username'] . "&obj=aceitarCliente'> <img src=\"permitir.png\" width=\"40\" height=\"40\"> </a></td>
				<td> <a href='eliminar.php?obj=eliminarUser&username=" . $row['username'] . "'> <img src=\"apagar.png\" width=\"40\" height=\"40\"> </a></td>
				</tr>";
				}
			}



			/*Tabela de clientes*/
			$sql = 'SELECT * FROM utilizadores WHERE cargo =' . CLIENTE;
			$retval = verificaConexao($conn, $sql);

			if (mysqli_num_rows($retval) > 0) {
				showTableHeader('Clientes');
				echo "<th>Expulsar</th> </tr>";

				while ($row = mysqli_fetch_assoc($retval)) {

					showInfoPessoal($row);

					echo "
					<td> <a href='eliminar.php?obj=eliminarUser&username=" . $row['username'] . "'> <img src=\"apagar.png\" width=\"40\" height=\"40\"> </a></td>
					</tr>";
				}
			}
			echo "</table>";


			function showTableHeader($titulo)
			{
				?>
				</table>

				<h2 class='table_title'><?php echo $titulo; ?></h2>

				<table class='tabela_gestao'>
					<tr>
						<th> Perfil </th>
						<th>Nome do Utilizador</th>
						<th>Tipo</th>
						<th>Telemóvel</th>
						<th>Editar</th>
						<?php
			}

			function showInfoPessoal($row)
			{

				echo "<tr>
				<td> <img src=" . usarImagemCorreta($row['imagem']) . " width=\"40\" height=\"40\"> </td>
				<td>" . $row['username'] . "</td>
				<td>" . getCargo($row['cargo']) . "</td>
				<td>" . $row['telemovel'] . "</td>
				<td> <a href='dados_pessoais.php?username=" . $row['username'] . "&obj=editarDadosUser'> <img src=\"editar.png\" width=\"40\" height=\"40\"> </a></td>";

			}
			mysqli_close($conn);
			?>
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