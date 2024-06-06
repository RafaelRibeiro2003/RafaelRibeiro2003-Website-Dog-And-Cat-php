<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>Reservas</title>
	<link rel='stylesheet' href='style.css'>
	<link rel='stylesheet' href='home.css'>
</head>
<?php
session_start();
include './constantes_funcoes.php';

sessionProtection();
$cargosAceites = array(CLIENTE, FUNCIONARIO, ADMINISTRADOR);
jobProtection($cargosAceites);
$objetivosAceites = array('gerirReservas');
objectiveProtection($objetivosAceites);

$cargo = $_SESSION['cargo'];

if (!isset($_GET['modoAdmin']) || $_GET['modoAdmin'] == "Pessoais")
	$_SESSION['modoAdmin'] = "Pessoais";
else
	$_SESSION['modoAdmin'] = "Todas";
$adm = $_SESSION['modoAdmin'];
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
				if ($cargo == ADMINISTRADOR) {
					?>
					<a href='gerir_users.php?obj=gerirUsers' class='button'>Gerir Elementos</a>
				<?php } ?>

				<a href='logout.php?obj=loggingOut' class='button'>Logout</a>
			</div>
		</header>

		<div id='body_tables'>
			<?php

			if ($cargo == ADMINISTRADOR) {
				if ($adm == 'Pessoais')
					echo "<a class='button' href='reservas.php?obj=gerirReservas&modoAdmin=Todas'>Mostar Todas as Reservas</a>";
				else
					if ($adm == 'Todas')
						echo "<a class='button' href='reservas.php?obj=gerirReservas&modoAdmin=Pessoais'>Mostar Apenas as Minhas Reservas</a>";
			}
			include './constMarcacao.php';
			include '../basedados/basedados.h';
			?>

			<form action='form_reservar.php?obj=fazerReserva' method='post' id='form'>
				<div class='input_container'>
					<label>Marcação:&nbsp;</label>
					<select name='marcacao' class='input' required>
						<option value='<?php echo CORTE; ?>' selected>Corte</option>
						<option value='<?php echo LAVAGEM; ?>'>Lavagem</option>
					</select>
				</div>
				<div class='input_container'>
					<label>Animal:&nbsp;</label>
					<select name='animal' class='input' required>
						<option value='<?php echo CAO; ?>' selected>Cão</option>
						<option value='<?php echo GATO; ?>'>Gato</option>
					</select>
				</div>

				<div class='container_tempo'>
					<label> Data:&nbsp;</label>
					<input type='date' name='data'
						min='<?php echo date_format(date_create(date('d-m-Y', time())), 'Y-m-d'); ?>' class='input'
						required />
					<label> Hora:&nbsp;</label>
					<input type='time' step=1800 min='09:00:00' max='18:00:00' name='hora' class='input' required />
				</div>
				<input type='submit' class='button' value='Marcar Reserva'></input>
			</form>
			<?php
			/*Tabela de Reservas autorizadas*/
			$sql = selectSQL(RESERVA_AUTORIZADA, $conn, $adm, $cargo);
			$retval = verificaConexao($conn, $sql);

			if (mysqli_num_rows($retval) > 0)
				showTableHeader('Reservas Aceites', $adm, $cargo);

			while ($row = mysqli_fetch_array($retval))
				showInfoGeral($row, $adm, $cargo, 'inacabada');


			/*Tabela de Reservas Concluídas*/
			$sql = selectSQL(RESERVA_CONCLUIDA, $conn, $adm, $cargo);
			$retval = verificaConexao($conn, $sql);

			if (mysqli_num_rows($retval) > 0)
				showTableHeader('Reservas Concluídas', $adm, $cargo);

			while ($row = mysqli_fetch_assoc($retval))
				showInfoGeral($row, $adm, $cargo, 'concluida');

			echo "</table>";


			//Começa funções
			function showTableHeader($titulo, $adm, $cargo)
			{
				?>
				</table>
				<h2 class='table_title'>
					<?php echo $titulo; ?>
				</h2>
				<table class='tabela_gestao'>
					<tr>
						<th> Nome do Funcionário</th>
						<th> Nome do Cliente </th>
						<th>Animal</th>
						<th>Tipo de Marcação</th>
						<th>Data</th>
						<th>Começo</th>
						<th>Conclusão</th>
						<?php
						if (!($cargo == CLIENTE && $titulo == 'Reservas Concluídas')) {
							?>
							<th>Editar</th>
							<th>Apagar</th>
						<?php }
						echo "</tr>";
			}

			function showInfoGeral($row, $adm, $cargo, $estado)
			{

				echo "<tr>
					<td>" . $row['funcionario'] . "</td>
					<td>" . $row['cliente'] . "</td>
					<td>" . getAnimal($row['animal']) . "</td>
					<td>" . getNome($row['marcacao']) . "</td>
					<td>" . $row['data_comb'] . "</td>
					<td>" . $row['hora_comb'] . "</td>
					<td>" . $row['hora_concl'] . "</td>";
				if (!($cargo == CLIENTE && $estado == 'concluida')) {
					echo "<td> <a href='editar_reserva.php?obj=editarMarcacao&reserva=" . $row['id'] . "&estado=$estado'><img
									src='editar.png' width=40 height=40> </a></td>
						<td> <a href='eliminar.php?obj=apagarReserva&reserva=" . $row['id'] . "'> <img
									src='apagar.png' width=40 height=40> </a></td>";
				}
				echo "</tr>";

			}



			function selectSQL($estado, $conn, $adm, $cargo)
			{
				$user = $_SESSION['username'];
				if ($estado == RESERVA_CONCLUIDA)
					$cond = '(data_comb < CURRENT_DATE OR (data_comb = CURRENT_DATE AND hora_comb < CURRENT_TIME))';
				else
					$cond = '(data_comb > CURRENT_DATE OR (data_comb = CURRENT_DATE AND hora_comb >= CURRENT_TIME))';

				if ($adm == "Pessoais")
					$cond = $cond . " AND (cliente = '$user' OR funcionario ='$user')";
				return "SELECT * FROM reservas WHERE $cond ORDER BY data_comb, hora_comb";
			}
			mysqli_close($conn); ?>
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