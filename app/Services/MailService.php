<?php
declare(strict_types=1);
namespace App\Services;

final class MailService
{
    public function send(string $to, string $subject, string $body): bool
    {
        // TODO: install phpmailer/phpmailer and send real email.
        return false;
    }
}
