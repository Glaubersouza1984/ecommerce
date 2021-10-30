<?php

  namespace Hcode; // onde está classe está

  use Rain\Tpl; // o Rain tem o namespace dele mesmo quando chamar o new tpl é do namespace Rain

  class Page {

    private $tpl;
    private $options = [];
    private $defaults = [
      "data"=>[]

    ];

    public function __construct($opts = array(), $tpl_dir = "/views/"){

      $this->options = array_merge($this->defaults, $opts); //vai mesclar os dois arrays e guardar dentor de options 

      $config = array(
        "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,//variável de ambient $_SERVER vai buscar a pasta no dir raiz
        "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
        "debug"         => false
      );
    
      Tpl::configure( $config );

      $this->tpl = new Tpl; //para ter acesso configurar como atributo da própria classe.

      $this->setData($this->options["data"]);

      $this->tpl->draw("header"); // draw vai desenhar o template na tela espera o nome do arquivo que vai chamar.

    }

    private function setData($data = array()){

      foreach ($data as $key => $value) {
        $this->tpl->assign($key, $value); //atribuições de variáveis que vão aparecer no template
      }

    }

    public function setTpl($name, $data = array(), $returnHTML = false){

      $this->setData($data);

      return $this->tpl->draw($name, $returnHTML);

    }

    public function __destruct(){

      $this->tpl->draw("footer");

    }


  }

?>