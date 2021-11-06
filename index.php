<?php
session_start();
require_once("vendor/autoload.php");  // do composer sempre vai existir onde está as pastas do projeto.

use \Slim\Slim; //namespace dentro do vendor tenho dezenas de classes qual eu quero.
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim(); // por conta das rotas criando uma nova aplicação do slim 

$app->config('debug', true);

$app->get('/', function() { //é uma rota o \ onde ela está

	$page = new Page();

	$page->setTpl("index");
	
});

$app->get('/admin', function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");
	
});

$app->get('/admin/login', function(){

	$page = new PageAdmin([
		"header"=>false, //Estamos desabilitando o header e o footer padrão que foi colocado no vendor
		"footer"=>false
	]);

	$page->setTpl("login");

});

$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;

});

$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");
	exit;

});

$app->run(); // pronto tudo carregado, vamos rodar.

?>