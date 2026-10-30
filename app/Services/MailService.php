<?php
declare(strict_types=1);
namespace App\Services;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

final class MailService
{
    private array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? \config('mail');
    }

    public function send(string $to, string $subject, string $body): bool
    {
        if (!$this->usesSmtp()) {
            return $this->writeLogMail($to, $subject, $body);
        }

        if (!class_exists(PHPMailer::class)) {
            throw new RuntimeException('PHPMailer chưa được cài đặt. Hãy chạy composer require phpmailer/phpmailer.');
        }

        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $mailer->Host = (string) ($this->config['host'] ?? '');
            $mailer->Port = (int) ($this->config['port'] ?? 587);
            $mailer->SMTPAuth = (string) ($this->config['username'] ?? '') !== '';
            $mailer->Username = (string) ($this->config['username'] ?? '');
            $mailer->Password = (string) ($this->config['password'] ?? '');
            $mailer->CharSet = 'UTF-8';

            $encryption = strtolower((string) ($this->config['encryption'] ?? ''));
            if ($encryption === 'tls') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($encryption === 'ssl') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            $mailer->setFrom(
                (string) ($this->config['from_email'] ?? 'no-reply@lobibus.local'),
                (string) ($this->config['from_name'] ?? 'LobiBus')
            );
            $mailer->addAddress($to);
            $mailer->Subject = $subject;
            $isHtml = $this->isHtml($body);
            $mailer->isHTML($isHtml);
            $mailer->Body = $body;
            $mailer->AltBody = $isHtml ? $this->plainText($body) : $body;

            return $mailer->send();
        } catch (PHPMailerException $exception) {
            throw new RuntimeException('Không thể gửi email: ' . $exception->getMessage(), 0, $exception);
        }
    }

    private function usesSmtp(): bool
    {
        return strtolower((string) ($this->config['mailer'] ?? 'log')) === 'smtp';
    }

    private function isHtml(string $body): bool
    {
        return $body !== strip_tags($body);
    }

    private function plainText(string $body): string
    {
        $body = preg_replace('/<br\s*\/?>/i', "\n", $body) ?? $body;
        $body = preg_replace('/<\/p>/i', "\n\n", $body) ?? $body;
        $body = preg_replace('/<\/(div|h1|h2|h3|li)>/i', "\n", $body) ?? $body;
        return trim(html_entity_decode(strip_tags($body), ENT_QUOTES, 'UTF-8'));
    }

    private function writeLogMail(string $to, string $subject, string $body): bool
    {
        $logPath = (string) ($this->config['log_path'] ?? dirname(__DIR__, 2) . '/public/uploads/mail.log');
        $directory = dirname($logPath);

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            return false;
        }

        $entry = sprintf(
            "[%s]\nTo: %s\nSubject: %s\n%s\n\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            $body
        );

        return file_put_contents($logPath, $entry, FILE_APPEND | LOCK_EX) !== false;
    }
}
