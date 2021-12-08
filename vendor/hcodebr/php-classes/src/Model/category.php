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

    Category::updateFile();

  }

  public function get($idcategory)
  {

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
      'idcategory'=>$idcategory
    ]);

    $this->setData($results[0]);

  }

  public function delete() // Não recebe parâmetro nenhum pois se espera que o objeto já está carregado.
  {

    $sql = new Sql();

    $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory",[
      'idcategory'=>$this->getidcategory() // pegar do próprio objeto palavra reservada this.
    ]);

    Category::updateFile();

  }

  public static function updateFile()
  {
    $categories = Category::listAll();

    $html = []; //array

    foreach ($categories as $row)
    {
        array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>'); // adicionar um item no array após ele ser criado.
    }

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html)); // Função para salvar as alterações no documento categories-menu, implode converte o array em string.
  }

  public function getProducts($related = true)
  {

    $sql = new Sql();

    if ($related === true){

      return $sql->select("SELECT * FROM tb_products WHERE idproduct IN(
          SELECT a.idproduct
          FROM tb_products a
          INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
          WHERE b.idcategory = :idcategory
          );
      ", [
        ':idcategory'=>$this->getidcategory()
      ]);

    } else {
     
     return $sql->select("SELECT * FROM tb_products WHERE idproduct NOT IN(
          SELECT a.idproduct
          FROM tb_products a
          INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
          WHERE b.idcategory = :idcategory
          );
      ", [
         ':idcategory'=>$this->getidcategory()
      ]);

    }

  }

  public function addProduct(Product $product)
  {

    $sql = new Sql();

    $sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory, :idproduct)", [
      ':idcategory'=>$this->getidcategory(),
      ':idproduct'=>$product->getidproduct()
    ]);

  }

  public function removeProduct(Product $product)
  {

    $sql = new Sql();

    $sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct", [
      ':idcategory'=>$this->getidcategory(),
      ':idproduct'=>$product->getidproduct()
    ]);

  }

   
}

?>