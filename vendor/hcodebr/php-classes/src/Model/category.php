<?php

namespace Hcode\Model; // onde a classe está é o namespace dela

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Category extends Model { // Classe model sabe fazer os geters e seters

  public static function listAll()
  {

    $sql = new Sql();

    return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

  }

  public function save()
  {

    $sql = new Sql();

    $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)",
     array(
      ":idcategory"=>$this->getidcategory(), 
      ":descategory"=>$this->getdescategory()
    ));

    $this->setData($results[0]); // resposta na posição 0 do array.

  }

  public function get($idcategory)
  {

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
      'idcategory'=>$idcategory
    ]);

    $this->setData($results[0]);

  }

  public function delete()
  {

    $sql = new Sql();

    $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory",[
      'idcategory'=>$this->getidcategory()
    ]);

  }

   
}

?>