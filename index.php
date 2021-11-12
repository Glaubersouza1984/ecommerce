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

	User::login($_POST["login"], $_POST["password"]); // Método estático chamado login User chamar o método login ::

	header("Location: /admin");
	exit;

});

$app->get('/admin/logout', function(){

	User::logout();

	header("Location: /admin/login");
	exit;

});

$app->get("/admin/users", function(){ // Se acessar a rota via get vai responder via html

	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));

});

$app->get("/admin/users/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});

$app->get("/admin/users/:iduser/delete", function($iduser){ 

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

}); 

$app->get("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});

$app->post("/admin/users/create", function(){ //acessar via post vai inserir no banco de dados

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; // se foi definido é 1 verdadeiro se não falso usuário admin

	$user->setData($_POST); // cria automático o objeto para o DAO

	$user->save();

	header("Location: /admin/users");
	exit;

}); 

$app->post("/admin/users/:iduser", function($iduser){ 

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

}); 

$app->run(); // pronto tudo carregado, vamos rodar.

?>