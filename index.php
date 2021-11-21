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

$app->get("/admin/users", function(){ // Se acessar a rota via get vai responder via html aqui listar usuários na tela

	User::verifyLogin();

	$users = User::listAll(); // aqui temos o array com toda a lista de usuário.

	$page = new PageAdmin();

	$page->setTpl("users", array( // aqui vamos passar o array para o template é um array com um monte de array dentro.
		"users"=>$users
	));

});

$app->get("/admin/users/create", function(){ // rota para acessar html users-create

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});

$app->get("/admin/users/:iduser/delete", function($iduser){ // rota para deletar usuário atentar ordem pra não sobrepor.

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

}); 

$app->get("/admin/users/:iduser", function($iduser){ // rota para acessar html users-update

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser); // get(int) para validar o tipo de dado é um método para consultar um usuário pelo id.

	$page = new PageAdmin();

	$page->setTpl("users-update", array( // aqui vamos passar o array para o template é um array com um monte de array dentro.
		"user"=>$user->getValues()
	));

});

$app->post("/admin/users/create", function(){ // rota para cadastrar usuário acessar via post vai inserir no banco de dados

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; // se foi definido é 1 verdadeiro se não falso usuário admin atentar no htlm users.create.

	$user->setData($_POST); // cria automático as variáveis para o DAO. No HTML usar os mesmos nomes da tabela no BD isso vai fazer criar um atributo para cada um dos valores que a gente tem.

	$user->save(); // vai efetuar o insert dentro do banco.

	header("Location: /admin/users"); // enviar para a tela novamente.
	exit;

}); 

$app->post("/admin/users/:iduser", function($iduser){ // rota para alterar usuário.

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

}); 

$app->get("/admin/forgot", function(){ // Rota para a tela esqueci a senha digite seu email

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUserd($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});

$app->run(); // pronto tudo carregado, vamos rodar.

?>