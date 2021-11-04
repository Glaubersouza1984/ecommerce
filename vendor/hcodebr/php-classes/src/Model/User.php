<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model { // Classe model sabe fazer os geters e seters

  Const SESSION = "User";

  public static function login($login, $password)
  {

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array( //Evitar SQL Injection :LOGIN
      ":LOGIN"=>$login //fazer o bind dos nosso parametros
    ));

    if (count($results) === 0) //se não retornou nenhum resultado estourar uma exceção.
    {
      throw new \Exception("Usuário inexistente ou senha inválida."); // colocar contra barra localizar exceções no diretório php principal.      
    }

    $data = $results[0];

    if (password_verify($password, $data["despassword"]) === true) //está função retorna verdadeiro ou falso par senha.
    {

      $user = new User();

      $user->setData($data);

      $_SESSION[User::SESSION] = $user->getValues();

      return $user;
     
    } else{
      throw new \Exception("Usuário inexistente ou senha inválida.");
    }

  }

  public static function verifyLogin($inadmin = true)
  {

    if (
      !isset($_SESSION[User::SESSION])
      ||
      !$_SESSION[User::SESSION]
      ||
      !(int)$_SESSION[User::SESSION]["iduser"] > 0
      ||
      (bool)$_SESSION[user::SESSION]["inadmin"] !== $inadmin
    ){

      header("Location: /admin/login/");
      exit;

    }

  }

  public static function logout()
  {

    $_SESSION[User::SESSION] = NULL;
  }

}

?>