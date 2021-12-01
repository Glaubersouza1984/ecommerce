<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app->get("/admin/categories", function(){ // Rota para acessar a página html das categorias 

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", [
		'categories'=>$categories	// Passar variável para o template através de um array.
	]);

});

$app->get("/admin/categories/create", function(){ // Rota tela que vai permitir criar categoria

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create"); // Template na pasta views.

});

$app->post("/admin/categories/create", function(){ // Rota para chamar método que vai criar categoria.

	User::verifyLogin();

	$category = new Category(); // Nova instância da classe category

	$category->setData($_POST); // category vamos setar a variável Post vai pegar os mesmos names da variável deste array global post e vai colocar no nosso objeto.

	$category->save(); // salvar no BD.

	header('Location: /admin/categories'); //depois de salvo redirecionar para tela categorias.
	exit;

});


$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	$category = new Category; //Criando um objeto da classe

	$category->get((int)$idcategory); // Carregar este objeto para ter certeza que ele existe lá no banco de dados.

	$category->delete(); // método para deletar.

	header('Location: /admin/categories'); //depois de salvo redirecionar para tela categorias.
	exit;

});


$app->get("/admin/categories/:idcategory", function($idcategory){ // HTML para trazer na tela.

	User::verifyLogin();

	$category = new Category(); // criamos a instância desta classe.

	$category->get((int)$idcategory); //Fazer um cast para converter para numérico vem em html em texto.
	
	$page = new PageAdmin(); // Tela para carregar as categorias.

	$page->setTpl("categories-update", [
		'category'=>$category->getValues() //Converter em um array.
	]);

});

$app->post("/admin/categories/:idcategory", function($idcategory){ // Aqui para fazer o update no banco de dados.

	User::verifyLogin();

	$category = new Category(); // objeto da classe.

	$category->get((int)$idcategory); //Fazer um cast para converter para numérico.
	
	$category->setData($_POST); // Carregar os novos dados que vem do formulário.

	$category->save(); // chamar método save.

	header('Location: /admin/categories');
	exit;

});

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category(); // objeto da classe.

	$category->get((int)$idcategory); // Recuperar o id que foi passado na função com get, fazer o cast para converter para número vem string através da url.
	$page = new Page();

	$page->setTpl("category", [ // Passar os dados desta categoria dado category com variável e getValues para pegar os valores.
		'category'=>$category->getValues(),
		'products'=>[] //array vazio por enquanto aqui vai ser colocado os produtos vindo do Banco de Dados.
	]);

});

?>