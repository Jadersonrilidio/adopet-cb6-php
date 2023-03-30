<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    // /**
    //  * 
    //  */
    // private PHPMailer $mail;

    // /**
    //  * 
    //  */
    // public function __construct(PHPMailer $mail)
    // {
    //     $this->mail = $mail;
    // }

    /**
     * 
     */
    public function sendMail(string $to, string $name, string $subject, string $body): bool
    {
        $mail = new PHPMailer(ENVIRONMENT === 'production' ? false : true);

        $mail->SMTPDebug = ENVIRONMENT === 'production' ? SMTP::DEBUG_OFF : SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Port = 465;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = env('EMAIL_ADDRESS', null);
        $mail->Password = env('EMAIL_PASSWORD', null);
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        $mail->setFrom(env('EMAIL_ADDRESS', null), 'Jay Rods');
        $mail->addAddress($to, $name);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;

        if (!$mail->send()) {
            return false;
        } else {
            if ($this->saveMail($mail)) {
                echo "Message saved!";
            }

            return true;
        }
    }

    /**
     * 
     */
    private function saveMail($mail)
    {
        $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

        $imapStream = imap_open($path, $mail->Username, $mail->Password);

        $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());

        imap_close($imapStream);

        return $result;
    }
}
