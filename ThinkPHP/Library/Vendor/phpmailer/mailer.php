<?php

ini_set("magic_quotes_runtime", 0);
require 'PHPMailerAutoload.php';

class mailer {

    private $mails;

    public function __construct() {
        $this->mails = new PHPMailer(true);
        $this->mails->IsSMTP();
        $this->mails->CharSet = 'UTF-8';
        $this->mails->SMTPAuth = true;
        $this->mails->Port = 25;
        $this->mails->Host = "smtp.163.com";
        $this->mails->Username = "123@qq.com";
        $this->mails->Password = "456";
        $this->mails->AddReplyTo($this->mails->Username, "service");
        $this->mails->From = $this->mails->Username;
        $this->mails->FromName = "service";
        $this->mails->AltBody = "To view the message, please use an HTML compatible email viewer!";
        $this->mails->WordWrap = 80;
        $this->mails->IsHTML(true);
    }

    /**
     * 
     * @param type $to
     * @param type $subject
     * @param type $body
     */
    public function getParam($to, $subject, $body) {
        $this->mails->AddAddress($to);
        $this->mails->Subject = $subject;
        $this->mails->Body = $body;
        return $this->sentMails();
    }

    /**
     * 发送邮件
     * @return boolean
     */
    private function sentMails() {
        try {
            $this->mails->Send();
            return $this->output(1, "success");
        } catch (phpmailerException $e) {
            return $this->output(0, $e->errorMessage());
        }
    }

    private function output($status, $msg) {
        return array("status" => $status, "msg" => $msg);
    }

}
