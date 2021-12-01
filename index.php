<?php
session_start();
require_once("vendor/autoload.php");  // do composer sempre vai existir onde está as pastas do projeto.

use \Slim\Slim; //namespace dentro do vendor tenho dezenas de classes qual eu quero.

$app = new Slim(); // por conta das rotas criando uma nova aplicação do slim 

$app->config('debug', true);

require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-Products.php");

$app->run(); // pronto tudo carregado, vamos rodar.

?>