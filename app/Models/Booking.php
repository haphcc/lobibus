<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Booking extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO bookings (user_id, trip_id, booking_code, customer_name, customer_phone, customer_email, total_amount, status)
             VALUES (:user_id, :trip_id, :booking_code, :customer_name, :customer_phone, :customer_email, :total_amount, :status)'
        );
        $stmt->execute([
            'user_id' => $data['user_id'] ?? null,
            'trip_id' => (int) ($data['trip_id'] ?? 0),
            'booking_code' => (string) ($data['booking_code'] ?? ''),
            'customer_name' => (string) ($data['customer_name'] ?? ''),
            'customer_phone' => (string) ($data['customer_phone'] ?? ''),
            'customer_email' => $data['customer_email'] ?? null,
            'total_amount' => (float) ($data['total_amount'] ?? 0),
            'status' => (string) ($data['status'] ?? 'pending'),
        ]);
        return (int) $this->db()->lastInsertId();
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db()->prepare(
            'SELECT b.*, t.departure_time, fl.name AS from_name, tl.name AS to_name
             FROM bookings b
             JOIN trips t ON t.id = b.trip_id
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             WHERE b.user_id = :user_id
             ORDER BY b.created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function allWithDetails(): array
    {
        $stmt = $this->db()->query(
            'SELECT b.*, t.departure_time, fl.name AS from_name, tl.name AS to_name,
                    p.status AS payment_status, p.method AS payment_method
             FROM bookings b
             JOIN trips t ON t.id = b.trip_id
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             LEFT JOIN payments p ON p.booking_id = b.id
             ORDER BY b.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function findWithDetails(int $id): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT b.*, t.departure_time, t.arrival_time, t.price,
                    fl.name AS from_name, tl.name AS to_name, bus.name AS bus_name,
                    p.method AS payment_method, p.status AS payment_status, p.transaction_code,
                    tk.ticket_code, tk.qr_code_path, tk.status AS ticket_status
             FROM bookings b
             JOIN trips t ON t.id = b.trip_id
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             JOIN buses bus ON bus.id = t.bus_id
             LEFT JOIN payments p ON p.booking_id = b.id
             LEFT JOIN tickets tk ON tk.booking_id = b.id
             WHERE b.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $booking = $stmt->fetch();
        if (!$booking) {
            return null;
        }

        $seatStmt = $this->db()->prepare(
            'SELECT s.seat_number, s.seat_type, bd.price
             FROM booking_details bd
             JOIN seats s ON s.id = bd.seat_id
             WHERE bd.booking_id = :booking_id
             ORDER BY s.seat_number ASC'
        );
        $seatStmt->execute(['booking_id' => $id]);
        $booking['seats'] = $seatStmt->fetchAll();

        return $booking;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db()->prepare('UPDATE bookings SET status = :status WHERE id = :id');
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }

    public function cancel(int $id): bool
    {
        return $this->updateStatus($id, 'cancelled');
    }
}
