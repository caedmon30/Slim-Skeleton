<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use App\Services\MailerService;

class EmailController
{
    protected MailerService $emailService;

    public function __construct(MailerService $emailService)
    {
        $this->emailService = $emailService;

    }

    public function sendEmail(array $data): bool
    {
        $to      = $data['email'] ?? 'cwalters@umd.edu';
        $subject = $data['subject'] ?? 'Test Email';
        $body    = $data['body'] ?? '<p>This is a test email.</p>';

        if ($this->emailService->sendMail($to, $subject, $body)) {
            return true;
        }
        return false;
    }
}