<?php

namespace HCODE;

use Rain\Tpl;

use PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer {

  const USERNAME = "glauber.souza@alunos.ifsuldeminas.edu.br";
  const PASSWORD = "A547725gml";
  const NAME_FROM = "Hcode Store";

  private $mail;

  public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
  {
    //Template

    $config = array(
      "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/email/", 
      "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
      "debug"         => false
    );
  
    Tpl::configure( $config ); // atributo da nossa classe

    $tpl = new Tpl; //para ter acesso configurar como atributo da própria classe.

    foreach ($data as $key => $value) {
      $tpl->assign($key, $value); //Criar as variáveis dentro do Template
    }

    $html = $tpl->draw($tplName, true); //True para jogar dentro da variável.

   $this->mail = new \PHPMailer;
 
    //Tell PHPMailer to use SMTP
    $this->mail->isSMTP();

    //Enable SMTP debugging
    //SMTP::DEBUG_OFF = off (for production use)
    //SMTP::DEBUG_CLIENT = client messages
    //SMTP::DEBUG_SERVER = client and server messages
    $this->mail->SMTPDebug = 0;

    //Set the hostname of the mail server
    $this->mail->Host = 'smtp.gmail.com';
    //Use `$this->mail->Host = gethostbyname('smtp.gmail.com');`
    //if your network does not support SMTP over IPv6,
    //though this may cause issues with TLS

    //Set the SMTP port number:
    // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
    // - 587 for SMTP+STARTTLS
    $this->mail->Port = 465;

    //Set the encryption mechanism to use:
    // - SMTPS (implicit TLS on port 465) or
    // - STARTTLS (explicit TLS on port 587)
    $this->mail->SMTPSecure = 0;

    //Whether to use SMTP authentication
    $this->mail->SMTPAuth = true;

    // Nome de usuário a ser usado para autenticação SMTP - use endereço de e-mail completo para gmail
    $this->mail->Username = Mailer::USERNAME; // Constante

    // Senha a ser usada para autenticação SMTP
    $this->mail->Password = Mailer::PASSWORD; // Constante

    // Defina de quem a mensagem deve ser enviada
    // Observe que com o gmail você só pode usar o endereço da sua conta (o mesmo que `Nome de usuário`)
    // ou aliases predefinidos que você configurou em sua conta.
    // Não use endereços enviados por usuários aqui
    $this->mail->setFrom (Mailer::USERNAME, Mailer::NAME_FROM); // Constante

    // Defina um endereço de resposta alternativo
    // Este é um bom lugar para colocar endereços enviados por usuários
    //$this->mail->addReplyTo ('replyto@example.com ',' Primeiro Último ');

    // Defina para quem a mensagem deve ser enviada
    $this->mail->addAddress ($toAddress, $toName); // Está vindo na variável do método construtor

    // Defina a linha de assunto
    $this->mail->Subject = $subject; // Está vindo na variável do método construtor.

    // Leia o corpo de uma mensagem HTML de um arquivo externo, converta imagens referenciadas em incorporadas,
    // converter HTML em um corpo alternativo de texto simples básico
    $this->mail->msgHTML ($html); // Variável HTML que vamos renderizar com RainTPL

    // Substitua o corpo do texto simples por um criado manualmente
    $this->mail->AltBody = 'Este é um corpo de mensagem de texto simples';

    // Anexe um arquivo de imagem
    $this->mail->addAttachment ('images/phpmailer_mini.png');
   
  }

  public function send()
  {

    return $this->mail->send();

  }

}


?>