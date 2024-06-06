<?php
	define("dbuser","root");
	define("dbpass","");
	define("dbnome","Dogandcat");
	
	$dbuser = 'root';
	$dbpass = '';
	$dbhost = 'localhost';
	
	//conectar ao servidor
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
	
	if(! $conn ){
		die('Could not connect: ' . mysqli_error($conn));
		exit;
	}	
		
	//Selecionamos nossa base de dados "Dogandcat"
	$con = mysqli_select_db($conn,'dogandcat');
	if(!$con){
			die('Could not connect: ' . mysqli_error($conn));
		exit;
	}
	
?>