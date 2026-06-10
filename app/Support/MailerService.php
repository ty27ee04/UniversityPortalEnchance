<?php

declare(strict_types=1);

namespace App\Support;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class MailerService
{
    private function createMailer(): PHPMailer
    {
        $mailer = new PHPMailer(true);
        $mailer->CharSet = 'UTF-8';

        $transport = strtolower((string) getenv('MAIL_TRANSPORT'));
        if ($transport === 'smtp') {
            $mailer->isSMTP();
            $mailer->Host = (string) getenv('MAIL_HOST');
            $mailer->SMTPAuth = true;
            $mailer->Username = (string) getenv('MAIL_USERNAME');
            $mailer->Password = (string) getenv('MAIL_PASSWORD');
            $mailer->SMTPSecure = getenv('MAIL_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS;
            $mailer->Port = (int) (getenv('MAIL_PORT') ?: 587);
        } else {
            $mailer->isMail();
        }

        $fromAddress = (string) (getenv('MAIL_FROM_ADDRESS') ?: 'no-reply@university-portal.local');
        $fromName = (string) (getenv('MAIL_FROM_NAME') ?: 'University Portal');
        $mailer->setFrom($fromAddress, $fromName);

        return $mailer;
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        try {
            $mailer = $this->createMailer();
            $mailer->addAddress($toEmail, $toName);
            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $htmlBody;
            $mailer->AltBody = strip_tags($htmlBody);

            return $mailer->send();
        } catch (Exception) {
            return false;
        }
    }

    public function sendRegistrationConfirmation(string $name, string $email): bool
    {
        return $this->send(
            $email,
            $name,
            'Welcome to the University Portal',
            '<h2>Registration successful</h2><p>Hello ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ', your student account is ready.</p>'
        );
    }

    public function sendEnrollmentConfirmation(string $name, string $email, string $course): bool
    {
        return $this->send(
            $email,
            $name,
            'Enrollment Received',
            '<h2>Enrollment received</h2><p>Hello ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ', we received your enrollment request for <strong>' . htmlspecialchars($course, ENT_QUOTES, 'UTF-8') . '</strong>.</p>'
        );
    }
}
