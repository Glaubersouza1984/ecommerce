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
  
  public static function checkList($list)
  {

    foreach ($list as &$row) {
     
      $p = new Product();
      $p->setData($row);
      $row = $p->getValues();

    }

    return $list;

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

    if (file_exists( // Se o arquivo existir.
      $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . //aqui é caminho de pasta do SO.
      "res" . DIRECTORY_SEPARATOR .
      "site" . DIRECTORY_SEPARATOR .
      "img" . DIRECTORY_SEPARATOR .
      "products" . DIRECTORY_SEPARATOR .
      $this->getidproduct() . ".jpg" // Foto vai ser o id do nosso produto
      )) {

        $url = "/res/site/img/products/" . $this->getidproduct() . ".jpg"; // aqui é url por isso utilizou barra vai carregar se a foto existir.

      } else {

        $url = "/res/site/img/product.jpg"; // Retornar uma foto padrão se a foto não existir.

      }

      return $this->setdesphoto($url); // Retornar a variável $url e setar dentro do objeto.

  }

  public function getValues() // Não quero chamar toda hora o método Photo somente quando chamar dentro de Products por isso reescrevemos o método.
  {

    $this->checkPhoto(); // Criar um método para ver se tem ou não foto faltando, se tiver vai carregar uma foto padrão.

    $values = parent::getValues();

    return $values;

  }

  public function setPhoto($file) // Método para salvar foto.
  {

    $extension = explode('.', $file['name']); // encontrou o ponto fez um array a partir dele. 
    $extension = end($extension); // A extenção é somente a última posição que ele achou deste array.
    
    switch ($extension) {

      case "jpg":
      case "jpeg":
        $image = imagecreatefromjpeg($file["tmp_name"]); // Função do GD para criar a imagem com biblioteca GD, tmp_name nome arquivo temporário que vem do servidor.
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

    imagejpeg($image, $dist); // Vou salvar image neste destino.

    imagedestroy($image); // destruir a imagem 

    $this->checkPhoto(); // Para o dado ficar carregado chamar o $this->checkPhoto

  }
    
}

?>