<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

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


$app->get("/admin/forgot", function(){ // Rota para a tela esqueci a senha digite seu email

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function(){ // Enviar via POST o e-mail

	$user = User::getForgot($_POST["email"]); // Método para Enviar E-mail

	header("Location: /admin/forgot/sent"); //Redirect
	exit;

});

$app->get("/admin/forgot/sent", function(){ // Abrir a Rota do Redirect acima.

	$page = new PageAdmin([ //Renderizar o template do Send
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){ //Rota para a Tela de Redefinir a Senha

	$user = User::validForgotDecrypt($_GET["code"]); //Validar 

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function(){ // Enviar para a mesma rota mas com método POST

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUserd($forgot["idrecovery"]); //Método para salvar no banco de dados data da alteração da senha.

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [ //Criptografar a senha para salvar no BD
		"cost"=>12
	]);

	$user->setPassword($password); //Salvar a Senha no Banco de Dados.

	$page = new PageAdmin([ //Setar template senha alterada com Sucesso.
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});

?>