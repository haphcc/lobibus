<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use Throwable;

final class TicketEmailService
{
    private MailService $mail;
    private Booking $bookings;

    public function __construct(?MailService $mail = null, ?Booking $bookings = null)
    {
        $this->mail = $mail ?? new MailService();
        $this->bookings = $bookings ?? new Booking();
    }

    public function sendForBooking(int $bookingId): bool
    {
        $booking = $this->bookings->getBookingDetailFull($bookingId);
        if ($booking === null) {
            return false;
        }

        $email = trim((string) ($booking['customer_email'] ?? ''));
        if ($email === '') {
            return false;
        }

        $attachments = [];
        $qrPath = trim((string) ($booking['qr_code_path'] ?? ''));
        $qrAbsolutePath = $qrPath !== '' ? dirname(__DIR__, 2) . '/public/assets/' . ltrim($qrPath, '/') : '';
        if ($qrAbsolutePath !== '' && is_file($qrAbsolutePath)) {
            $attachments[] = [
                'path' => $qrAbsolutePath,
                'name' => 'lobibus-' . (string) ($booking['ticket_code'] ?? 'ticket') . '.svg',
            ];
        }

        try {
            return $this->mail->send(
                $email,
                'Ve xe LobiBus ' . (string) ($booking['ticket_code'] ?? ''),
                $this->body($booking),
                $attachments
            );
        } catch (Throwable) {
            return false;
        }
    }

    private function body(array $booking): string
    {
        $seatNumbers = array_column($booking['seats'] ?? [], 'seat_number');
        $departure = !empty($booking['departure_time']) ? date('H:i d/m/Y', strtotime((string) $booking['departure_time'])) : '-';
        $arrival = !empty($booking['arrival_time']) ? date('H:i d/m/Y', strtotime((string) $booking['arrival_time'])) : '-';

        return '<div style="font-family:Arial,sans-serif;line-height:1.6;color:#0f172a">'
            . '<h2 style="margin:0 0 12px;color:#0f766e">Ve xe LobiBus</h2>'
            . '<p>Xin chao ' . $this->e((string) ($booking['customer_name'] ?? '')) . ',</p>'
            . '<p>LobiBus gui ma ve va QR rieng cua ban. QR dinh kem chua thong tin ma ve va noi dung chuyen di.</p>'
            . '<table style="border-collapse:collapse;width:100%;max-width:640px">'
            . $this->row('Ma ve', (string) ($booking['ticket_code'] ?? '-'))
            . $this->row('Ma booking', (string) ($booking['booking_code'] ?? '-'))
            . $this->row('Trang thai booking', (string) ($booking['status'] ?? '-'))
            . $this->row('Tuyen', (string) (($booking['from_name'] ?? '-') . ' -> ' . ($booking['to_name'] ?? '-')))
            . $this->row('Gio di', $departure)
            . $this->row('Gio den', $arrival)
            . $this->row('Ghe', $seatNumbers ? implode(', ', $seatNumbers) : '-')
            . $this->row('Tong tien', number_format((float) ($booking['total_amount'] ?? 0), 0, ',', '.') . 'd')
            . '</table>'
            . '<p style="margin-top:16px">Vui long mang theo ma ve/QR khi len xe.</p>'
            . '</div>';
    }

    private function row(string $label, string $value): string
    {
        return '<tr>'
            . '<td style="padding:8px 10px;border:1px solid #e2e8f0;background:#f8fafc;font-weight:600;width:180px">' . $this->e($label) . '</td>'
            . '<td style="padding:8px 10px;border:1px solid #e2e8f0">' . $this->e($value) . '</td>'
            . '</tr>';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
