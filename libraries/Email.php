<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//Inclui a classe PHPmailer
require_once("phpmailer/PHPMailerAutoload.php");

/**
 * Email
 *
 * Classe para abstrair o PHPMailer
 *
 * @author  Henrique de Castro
 * @since   10/2009
 */
class Email extends PHPMailer{

    //Instância do CI
    private $__CI;

    /**
     * Construtor
     *
     * Inicializa e parametriza o phpmailer
     *
     * @access public
     * @author Henrique de Castro
     * @since  10/2009
     * @return void
     */
    public function __construct($params = array()) {

        //Busca a instância do CI
        $this->__CI =& get_instance();

        //Carrega o arquivo de configurações
        $this->__CI->config->load("email_config");
        
        //Parametriza o phpmailer
        $this->From     = (isset($params["from"])      ? $params["from"]      : $this->__CI->config->item("from"));
        $this->Sender   = (isset($params["from"])      ? $params["from"]      : $this->__CI->config->item("from"));
        $this->FromName = (isset($params["from_name"]) ? $params["from_name"] : $this->__CI->config->item("from_name"));
        $this->Port     = (isset($params["port"])      ? $params["port"]      : $this->__CI->config->item("port"));
        parent::SetLanguage("br");
        
        //Verifica se deve adicionar o replyTo
        if(isset($params["from"]) && isset($params["from_name"]))
            $this->AddReplyTo($params["from"], $params["from_name"]);

        //Seta as opções de SMTP
        parent::IsSMTP(true);
        parent::IsHTML(true);
        $this->SMTPAuth = true;
        $this->Host     = $this->__CI->config->item("host");
        $this->Username = (isset($params["from"])     ? $params["from"]     :   $this->__CI->config->item("from"));
        $this->Password = (isset($params["password"]) ? $params["password"] :   $this->__CI->config->item("password"));
    }

    /**
     * envia
     *
     * Envia um e-mail
     *
     * @access public
     * @author Henrique de Castro
     * @since  10/2009
     * @param  array||string
     * @param  array||string
     * @return boolean||string
     */
    public function envia($nomes, $emails) {
            
        return true;
        
        //Retira a codificação UTF8 das mensagens e do assunto
        $this->Body     = utf8_decode($this->Body);
        $this->AltBody  = utf8_decode($this->AltBody);
        $this->Subject  = utf8_decode($this->Subject);
        $this->FromName = utf8_decode($this->FromName);

        //Remove os destinatários duplicados
        $emails = (is_array($emails) ? array_unique($emails) : $emails);

        //Loop nos remetentes
        foreach((array)$emails as $key => $destinatario) {

            //Busca o nome
            $nome = (is_array($nomes) ? $nomes[$key] : $nomes);

            //Adiciona o destinatário
            if($destinatario)
                parent::AddAddress($destinatario, $nome);

            //Envia a mensagem
            if(!parent::Send())
                //Em caso de erro no envio, retorna mensagem de erro
                return $this->ErrorInfo;

            //Limpa os destinatários
            parent::ClearAddresses();

        }

        //Tudo ok
        return true;
    }

}

?>