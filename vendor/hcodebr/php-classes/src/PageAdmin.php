<?php 

namespace Hcode;

class PageAdmin extends Page {

	public function __construct($opts = array(), $tpl_dir = "/views/admin/")
	{

		parent::__construct($opts, $tpl_dir); // Conceito de herança extend tudo do construtor da classe page com variável diretório diferente.

	}

}

?>