<?php

namespace Hcode\Model; // onde a classe está é o namespace dela

use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model { // Classe model sabe fazer os geters e seters

  Const SESSION = "User";

  public static function login($login, $password)
  {

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array( //Evitar SQL Injection :LOGIN
      ":LOGIN"=>$login //fazer o bind dos nosso parametros vai ser a variável login do nosso parâmetro
    ));

    if (count($results) === 0) //se não retornou nenhum resultado estourar uma exceção.
    {
      throw new \Exception("Usuário inexistente ou senha inválida."); // colocar contra barra localizar exceções no diretório php principal pois não está dentro de Model.      
    }

    $data = $results[0];

    if (password_verify($password, $data["despassword"]) === true) //está função retorna verdadeiro ou falso para senha.
    {

      $user = new User(); // se verdadeiro vamos criar um objeto deste usuário.

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

  public static function listAll()
  {

    $sql = new Sql();

    return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

  }

  public function save() // Este método não pode ser estático pois vai ter acesso as informações que estão nos atributos.
  {

    $sql = new Sql();

    $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
     array(
      ":desperson"=>$this->getdesperson(), // todos estes get foram gerados pelo setdata.
      ":deslogin"=>$this->getdeslogin(), //":deslogin"=>this isso é o bind para associar a chave e valor.
      ":despassword"=>$this->getdespassword(),
      ":desemail"=>$this->getdesemail(),
      ":nrphone"=>$this->getnrphone(),
      ":inadmin"=>$this->getinadmin()
    ));

    $this->setData($results[0]); // resposta na posição 0 do array.

  }

  public function get($iduser)
  {

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
      ":iduser"=>$iduser
    ));

    $this->setData($results[0]); // resposta posição 0 do array.

  }

  public function update()
  {

    $sql = new Sql();

    $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
     array(
      ":iduser"=>$this->getiduser(), 
      ":desperson"=>$this->getdesperson(),
      ":deslogin"=>$this->getdeslogin(),
      ":despassword"=>$this->getdespassword(),
      ":desemail"=>$this->getdesemail(),
      ":nrphone"=>$this->getnrphone(),
      ":inadmin"=>$this->getinadmin()
    ));

    $this->setData($results[0]);

  }

  public function delete()
  {

    $sql = new Sql();

    $sql->query("CALL sp_users_delete(:iduser)", array(
      ":iduser"=>$this->getiduser()
    ));
  }
  
}

?>