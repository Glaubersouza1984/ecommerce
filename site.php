<?php

use \Hcode\Page;
use \Hcode\Model\Product;

$app->get('/', function() { //é uma rota o \ onde ela está

	$products = Product::listAll();
	
	$page = new Page();

	$page->setTpl("index", [
		'products'=>Product::checkList($products)
	]);
	
});




?>