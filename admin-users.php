<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;

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


?>