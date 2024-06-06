<?php
define ('LAVAGEM',1);
define ('CORTE',2);

define('GATO', 'gato');
define('CAO', 'cao');

function getAnimal($animal){
	switch ($animal) {
		case GATO:
			return 'Gato';
		case CAO:
			return 'Cão';
		default:
			return 'Desconhecido';
	}
}

function getNome(int $num)
{
	switch ($num) {
		case LAVAGEM:
			return 'Lavagem';
		case CORTE:
			return 'Corte';
		default:
			return 'Desconhecido';
	}
}
function getDuracao(int $num)
{
	switch ($num) {
		case LAVAGEM:
			return 30;
		case CORTE:
			return 60;
		default:
			return 'Desconhecido';
	}
}
function getPreco(int $num)
{
	switch ($num) {
		case LAVAGEM:
			return 4.25;
		case CORTE:
			return 13.70;
		default:
			return 'Desconhecido';
	}
}

function getDescricao(int $num)
{
	switch ($num) {
		case LAVAGEM:
			return 'Faremos uma limpeza de alto abaixo extremamente confortável para o seu animal de estimação.';
		case CORTE:
			return 'Faremos um corte personalizado ao gosto do dono desde que esteja dentro dos padrões de bons tratos aos animais.';
		default:
			return 'Desconhecido';
	}
}

?>