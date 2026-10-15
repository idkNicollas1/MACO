<?php

namespace MacoBackend\Services;
use PHPMailer\PHPMailer\PHPMailer;
use Exception;

class EmailService
{    
    /**
     * Função que envia e-mail
     * 
     * @param $title
     * @param $html
     * @param $address
     * @param $name
     */
    public static function sendMail(string $title, string $html, string $address, string $name) {
        try {
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = getenv('EMAIL_HOST');
            $mail->Port = getenv('EMAIL_PORT');
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('EMAIL_USER');
            $mail->Password = getenv('EMAIL_PASSWORD');

            $mail->setFrom(getenv('EMAIL_FROM'), 'Maco - UNIFAE');
            
            $mail->addAddress($address, $name);            
            // $mail->addReplyTo('', ''); -- Copia oculta

            $mail->Subject = utf8_decode($title);            
            $mail->msgHTML(utf8_decode($html), __DIR__);            

            if (!$mail->send()) {
                throw new Exception('Erro ao enviar o e-mail: ' . $mail->ErrorInfo);
            }

            return [
                'status' => 'success',
                'message' => 'Email successfully sent',
            ];
        } catch(Exception $e) {
            return [
                'status' => 'Error',
                'message' => $e->getMessage(),
            ];
        }
    }            
}