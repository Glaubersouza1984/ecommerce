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

    $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
      ":idproduct"=>$this->getidproduct(), 
      ":desproduct"=>$this->getdesproduct(),
      ":vlprice"=>$this->getvlprice(),
      ":vlwidth"=>$this->getvlwidth(),
      ":vlheight"=>$this->getvlheight(),
      ":vllength"=>$this->getvllength(),
      ":vlweight"=>$this->getvlweight(),
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


  public function checkPhoto()
  {

    if (file_exists( 
      $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . //aqui é caminho de pasta do SO.
      "res" . DIRECTORY_SEPARATOR .
      "site" . DIRECTORY_SEPARATOR .
      "img" . DIRECTORY_SEPARATOR .
      "products" . DIRECTORY_SEPARATOR .
      $this->getidproduct() . ".jpg"
      )) {

        $url = "/res/site/img/products/" . $this->getidproduct() . ".jpg"; // aqui é url.

      } else {

        $url = "/res/site/img/product.jpg";

      }

      return $this->setdesphoto($url);

  }

  public function getValues()
  {

    $this->checkPhoto();

    $values = parent::getValues();

    return $values;

  }

  public function setPhoto($file)
  {

    $extension = explode('.', $file['name']); // encontrou o ponto fez um array a partir dele. 
    $extension = end($extension);
    
    switch ($extension) {

      case "jpg":
      case "jpeg":
        $image = imagecreatefromjpeg($file["tmp_name"]);
      break;

      case "gif":
        $image = imagecreatefromgif($file["tmp_name"]);  
      break;

      case "png":
        $image = imagecreatefrompng($file["tmp_name"]);
      break;

    }

    $dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . //aqui é caminho de pasta do SO.
    "res" . DIRECTORY_SEPARATOR .
    "site" . DIRECTORY_SEPARATOR .
    "img" . DIRECTORY_SEPARATOR .
    "products" . DIRECTORY_SEPARATOR .
    $this->getidproduct() . ".jpg";

    imagejpeg($image, $dist);

    imagedestroy($image);

    $this->checkPhoto();

  }
    
}

?>