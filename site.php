<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;

$app->get('/', function() { //é uma rota o \ onde ela está

	$products = Product::listAll();
	
	$page = new Page();

	$page->setTpl("index", [
		'products'=>Product::checkList($products)
	]);
	
});

$app->get("/categories/:idcategory", function($idcategory){

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$category = new Category(); // objeto da classe.

	$category->get((int)$idcategory); // Recuperar o id que foi passado na função com get, fazer o cast para converter para número vem string através da url.

	$pagination = $category->getProductsPage($page); 

	$pages = [];

	for ($i=0; $i <=$pagination['pages'] ; $i++) { 
		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
			'page'=>$i
		]);
	}

	$page = new Page();

	$page->setTpl("category", [ // Passar os dados desta categoria dado category com variável e getValues para pegar os valores.
		'category'=>$category->getValues(),
		'products'=>$pagination["data"],
		'pages'=>$pages
	]);

});