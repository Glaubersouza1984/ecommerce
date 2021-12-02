<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function(){

  User::verifyLogin();

  $products = Product::listAll();

  $Page = new PageAdmin();

  $Page->setTpl("products", [
    "products"=>$products
  ]);

});

$app->get("/admin/products/create", function(){

  User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("products-create"); 

});

$app->post("/admin/products/create", function(){// Rota para chamar método que vai criar produto

  User::verifyLogin();

	$product = new Product();  // Nova instância da classe Product

  $product->setData($_POST); // product vamos setar a variável Post vai pegar os mesmos names da variável deste array global post e vai colocar no nosso objeto.

  $product->save(); // salvar no BD.

  header('Location: /admin/products'); //depois de salvo redirecionar para tela produtos.
	exit;

});

$app->get("/admin/products/:idproduct", function($idproduct){ // HTML para trazer na tela.

	User::verifyLogin();

	$product = new Product(); // criamos a instância desta classe.

	$product->get((int)$idproduct); //Fazer um cast para converter para numérico vem em html em texto.
	
	$page = new PageAdmin(); // Tela para carregar as categorias.

	$page->setTpl("products-update", [
		'product'=>$product->getValues() //Converter em um array.
	]);

});

$app->post("/admin/products/:idproduct", function($idproduct){ // HTML para trazer na tela.

	User::verifyLogin();

	$product = new Product(); // criamos a instância desta classe.

	$product->get((int)$idproduct); //Fazer um cast para converter para numérico vem em html em texto.
	
  $product->setData($_POST);

  $product->save(); 
  
  $product->setPhoto($_FILES["file"]);

  header('Location: /admin/products');
  exit;

});

$app->get("/admin/products/:idproduct/delete", function($idproduct){

	$product = new Product; //Criando um objeto da classe

	$product->get((int)$idproduct); // Carregar este objeto para ter certeza que ele existe lá no banco de dados.

	$product->delete(); // método para deletar.

	header('Location: /admin/products'); //depois de salvo redirecionar para tela categorias.
	exit;

});

?>