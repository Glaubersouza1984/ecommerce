<?php 

require_once("vendor/autoload.php");  // do composer sempre vai existir onde está as pastas do projeto.

use \Slim\Slim; //namespace dentro do vendor tenho dezenas de classes qual eu quero.
use \Hcode\Page;
use \Hcode\PageAdmin;

$app = new Slim(); // por conta das rotas criando uma nova aplicação do slim 

$app->config('debug', true);

$app->get('/', function() { //é uma rota o \ onde ela está

	$page = new Page();

	$page->setTpl("index");
	
});

$app->get('/admin/', function() {

	$page = new PageAdmin();

	$page->setTpl("index");
	
});

$app->run(); // pronto tudo carregado, vamos rodar.

?>