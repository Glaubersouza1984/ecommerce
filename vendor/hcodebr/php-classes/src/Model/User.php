<?php

namespace Hcode\Model; // onde a classe está é o namespace dela

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model { // Classe model sabe fazer os geters e seters

  Const SESSION = "User";
  Const SECRET = "HcodePhp7_Secret";

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

  public static function getForgot($email, $inadmin = true) // Classe para Enviar E-Mail
{
     $sql = new Sql();
     $results = $sql->select("
         SELECT *
         FROM tb_persons a
         INNER JOIN tb_users b USING(idperson)
         WHERE a.desemail = :email;
     ", array(
         ":email"=>$email // BindParameters
     ));
     if (count($results) === 0) 
     {
         throw new \Exception("Não foi possível recuperar a senha.");
     }
     else
     {
         $data = $results[0]; // Dados do usário na Posição 0

         $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
             ":iduser"=>$data['iduser'],
             ":desip"=>$_SERVER['REMOTE_ADDR'] //Função para Gravar IP usuário.
         ));
         if (count($results2) === 0)
         {
             throw new \Exception("Não foi possível recuperar a senha.");
         }
         else
         {
             $dataRecovery = $results2[0];
            
             // A partir daqui vamos gerar um código criptografado que é o id recovery que veio da procedure.

             $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
             $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
             $result = base64_encode($iv.$code);
             if ($inadmin === true) {
                 $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";
             } else {
                 $link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";
             } 
            
             $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
              "name"=>$data['desperson'],
              "link"=>$link
            ));				
    
            $mailer->send();
             return $link;
         }
     }
 }

  public static function validForgotDecrypt($result) //Validar e descriptografar o código enviado anteriormente.
  {
      $result = base64_decode($result);
      $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
      $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');;
      $idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);
     
      $sql = new Sql();
      $results = $sql->select("
          SELECT *
          FROM tb_userspasswordsrecoveries a
          INNER JOIN tb_users b USING(iduser)
          INNER JOIN tb_persons c USING(idperson)
          WHERE
          a.idrecovery = :idrecovery
          AND
          a.dtrecovery IS NULL
          AND
          DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
      ", array(
          ":idrecovery"=>$idrecovery
      ));
     
      if (count($results) === 0)
      {
          throw new \Exception("Não foi possível recuperar a senha.");
      }
      else
      {
          return $results[0];
      }
  }

  public static function setForgotUserd($idrecovery)
  {

    $sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
    ));

  }

  public function setPassword($password) //Salvar a Senha no Banco de Dados.
  {

    $sql = new Sql();

    $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
      ":password"=>$password,
      ":iduser"=>$this->getiduser()
    ));
  }
   
}

?>