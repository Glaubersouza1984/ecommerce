<?php

namespace Hcode\Model; // onde a classe está é o namespace dela

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Product extends Model { // Classe model sabe fazer os geters e seters

  public static function listAll()
  {

    $sql = new Sql();

    return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");

  }

  public function save()
  {

    $sql = new Sql();

    $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)",
     array(
      ":idproduct"=>$this->getidproduct(), 
      ":desproduct"=>$this->getdesproduct(),
      ":vlprice"=>$this->getvlprice(),
      ":vlwidth"=>$this->getvlwidth(),
      ":vlheight"=>$this->getvlheight(),
      ":vllength"=>$this->getvllength(),
      ":vlweigth"=>$this->getvlweigth(),
      "desurl"=>$this->getdesurl()
    ));

    $this->setData($results[0]); // resposta na posição 0 do array.

  }

  public function get($idproduct)
  {

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
      'idproduct'=>$idproduct
    ]);

    $this->setData($results[0]);

  }

  public function delete() // Não recebe parâmetro nenhum pois se espera que o objeto já está carregado.
  {

    $sql = new Sql();

    $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct",[
      'idproduct'=>$this->getidproduct() // pegar do próprio objeto palavra reservada this.
    ]);

  }
    
}

?>