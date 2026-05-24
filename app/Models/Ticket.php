<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Ticket extends Model
{
    public function createTicket(array $data): int
    {
        return $this->create($data);
    }

    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO tickets (booking_id, ticket_code, qr_code_path, status)
             VALUES (:booking_id, :ticket_code, :qr_code_path, :status)'
        );
        $stmt->execute([
            'booking_id' => (int) ($data['booking_id'] ?? 0),
            'ticket_code' => (string) ($data['ticket_code'] ?? ''),
            'qr_code_path' => $data['qr_code_path'] ?? null,
            'status' => (string) ($data['status'] ?? 'valid'),
        ]);

        return (int) $this->db()->lastInsertId();
    }

    public function generateTicketCode(): string
    {
        do {
            $code = 'TICKET-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $stmt = $this->db()->prepare('SELECT COUNT(*) FROM tickets WHERE ticket_code = :code');
            $stmt->execute(['code' => $code]);
        } while ((int) $stmt->fetchColumn() > 0);

        return $code;
    }

    public function getTicketByBooking(int $bookingId): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM tickets WHERE booking_id = :booking_id ORDER BY id ASC LIMIT 1');
        $stmt->execute(['booking_id' => $bookingId]);
        $ticket = $stmt->fetch();
        return $ticket ?: null;
    }

    public function updateQrPath(int $id, string $path): bool
    {
        $stmt = $this->db()->prepare('UPDATE tickets SET qr_code_path = :path WHERE id = :id');
        return $stmt->execute(['id' => $id, 'path' => $path]);
    }

    public function updateStatus(int $bookingId, string $status): bool
    {
        $stmt = $this->db()->prepare('UPDATE tickets SET status = :status WHERE booking_id = :booking_id');
        return $stmt->execute(['booking_id' => $bookingId, 'status' => $status]);
    }

    public function getTicketByCode(string $code): ?array
    {
        return $this->findByCode($code);
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT tk.*, b.booking_code, b.user_id, b.trip_id, b.customer_name, b.customer_phone,
                    b.customer_email, b.total_amount, b.status AS booking_status,
                    t.departure_time, t.arrival_time, fl.name AS from_name, tl.name AS to_name
             FROM tickets tk
             JOIN bookings b ON b.id = tk.booking_id
             JOIN trips t ON t.id = b.trip_id
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             WHERE tk.ticket_code = :code'
        );
        $stmt->execute(['code' => $code]);
        $ticket = $stmt->fetch();
        return $ticket ?: null;
    }
}
