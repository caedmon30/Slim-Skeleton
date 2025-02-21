<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    private PHPMailer $mail;

    public function __construct(PHPMailer $mail)
    {
        $this->mail = $mail;
    }

    public function sendMail(string $to, string $subject, string $body): bool
    {
        try {
            $this->mail->clearAddresses(); // Clear previous recipients
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->isHTML(true);

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Mail error: " . $e->getMessage());
            return false;
        }
    }
}
