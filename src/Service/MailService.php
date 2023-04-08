<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    /**
     * 
     */
    private PHPMailer $mail;

    /**
     * 
     */
    public function __construct(PHPMailer $mail)
    {
        $this->mail = $mail;

        $this->config();
    }

    /**
     * 
     */
    private function config(): void
    {
        $this->mail->SMTPDebug = ENVIRONMENT === 'production' ? SMTP::DEBUG_OFF : SMTP::DEBUG_SERVER;
        $this->mail->isSMTP();
        $this->mail->Port = 465;
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = env('EMAIL_ADDRESS', null);
        $this->mail->Password = env('EMAIL_PASSWORD', null);
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    }

    /**
     * 
     */
    public function sendMail(string $to, string $name, string $subject, string $body): bool
    {
        $this->mail->setFrom(env('EMAIL_ADDRESS', null), 'Jay Rods');
        $this->mail->addAddress($to, $name);
        $this->mail->Subject = $subject;
        $this->mail->Body = $body;
        $this->mail->AltBody = $body;

        if (!$this->mail->send()) {
            return false;
        } else {
            if ($this->saveMail($this->mail)) {
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
