<?php

use \Hcode\Page;

$app->get('/', function() { //é uma rota o \ onde ela está

	$page = new Page();

	$page->setTpl("index");
	
});




?>