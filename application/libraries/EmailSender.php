<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * Email Sender
 */
class EmailSender
{
  private $ci; // CI Instance

  public function __construct() {
    $this->ci = &get_instance();
    $this->ci->load->library('email');

    $this->ci->load->config('email');
    $this->ci->email->initialize($this->ci->config->item('email'));
  }

  public function send($subject,$mail_html,$destinatario = '',$filename='',$message = '') {
    $email_body=$mail_html;
    $this->ci->email->set_mailtype("html");
    $this->ci->email->from('hr@iblinfotech.com', 'IBL INFOTECH');
    $this->ci->email->to($destinatario);
    // $this->ci->email->cc('another@another-example.com');
    // $this->ci->email->bcc('them@their-example.com');

    $this->ci->email->subject($subject);
    $this->ci->email->message($message);
    if(!empty( $email_body)){
      $this->ci->email->attach($email_body, 'attachment', $filename, 'application/pdf');
    }
    $this->ci->email->send();
    $this->ci->email->clear(TRUE);
  }
}
?>