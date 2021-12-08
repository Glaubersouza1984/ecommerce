<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

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

$app->get("/admin/categories/:idcategory/products", function($idcategory){

	User::verifyLogin();

	$category = new Category(); // objeto da classe.

	$category->get((int)$idcategory); // Recuperar o id que foi passado na função com get, fazer o cast para converter para número vem string através da url.
	$page = new PageAdmin();

	$page->setTpl("categories-products", [ 
		'category'=>$category->getValues(),
		'productsRelated'=>$category->getProducts(),
		'productsNotRelated'=>$category->getProducts(false)
	]);
	
});

	
	$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct){

		User::verifyLogin();
	
		$category = new Category(); // objeto da classe.
	
		$category->get((int)$idcategory); 

		$product = new Product();

		$product->get((int)$idproduct); 

		$category->addProduct($product);

		header("Location: /admin/categories/".$idcategory."/products");
		exit;

});


$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct){

	User::verifyLogin();

	$category = new Category(); // objeto da classe.

	$category->get((int)$idcategory); 

	$product = new Product();

	$product->get((int)$idproduct); 

	$category->removeProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;

});


?>