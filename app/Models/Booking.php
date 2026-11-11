<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Booking extends Model
{
    public function createBooking(array $data): int
    {
        return $this->create($data);
    }

    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO bookings (user_id, trip_id, booking_code, booking_group_code, trip_type, direction, customer_name, customer_phone, customer_email, total_amount, status)
             VALUES (:user_id, :trip_id, :booking_code, :booking_group_code, :trip_type, :direction, :customer_name, :customer_phone, :customer_email, :total_amount, :status)'
        );
        $stmt->execute([
            'user_id' => $data['user_id'] ?? null,
            'trip_id' => (int) ($data['trip_id'] ?? 0),
            'booking_code' => (string) ($data['booking_code'] ?? ''),
            'booking_group_code' => $data['booking_group_code'] ?? null,
            'trip_type' => (string) ($data['trip_type'] ?? 'oneway'),
            'direction' => (string) ($data['direction'] ?? 'outbound'),
            'customer_name' => (string) ($data['customer_name'] ?? ''),
            'customer_phone' => (string) ($data['customer_phone'] ?? ''),
            'customer_email' => $data['customer_email'] ?? null,
            'total_amount' => (float) ($data['total_amount'] ?? 0),
            'status' => (string) ($data['status'] ?? 'pending'),
        ]);
        return (int) $this->db()->lastInsertId();
    }

    public function generateBookingCode(): string
    {
        do {
            $code = 'LB-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $stmt = $this->db()->prepare('SELECT COUNT(*) FROM bookings WHERE booking_code = :code');
            $stmt->execute(['code' => $code]);
        } while ((int) $stmt->fetchColumn() > 0);

        return $code;
    }

    public function generateBookingGroupCode(): string
    {
        do {
            $code = 'RT-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $stmt = $this->db()->prepare('SELECT COUNT(*) FROM bookings WHERE booking_group_code = :code');
            $stmt->execute(['code' => $code]);
        } while ((int) $stmt->fetchColumn() > 0);

        return $code;
    }

    public function getBookingById(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM bookings WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $booking = $stmt->fetch();
        return $booking ?: null;
    }

    public function getBookingByCode(string $code): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM bookings WHERE booking_code = :code');
        $stmt->execute(['code' => $code]);
        $booking = $stmt->fetch();
        return $booking ?: null;
    }

    public function getBookingsByUser(int $userId): array
    {
        return $this->findByUser($userId);
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->db()->prepare(
            'SELECT b.*, t.departure_time, t.arrival_time, fl.name AS from_name, tl.name AS to_name,
                    COUNT(bd.id) AS seat_count,
                    GROUP_CONCAT(s.seat_number ORDER BY s.seat_number SEPARATOR ", ") AS seat_numbers
             FROM bookings b
             JOIN trips t ON t.id = b.trip_id
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             LEFT JOIN booking_details bd ON bd.booking_id = b.id
             LEFT JOIN seats s ON s.id = bd.seat_id
             WHERE b.user_id = :user_id
             GROUP BY b.id, t.departure_time, t.arrival_time, fl.name, tl.name
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

    public function getBookingDetailFull(int $id): ?array
    {
        return $this->findWithDetails($id);
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
        $booking['related_bookings'] = $this->findRelatedByGroup($booking);

        return $booking;
    }

    public function findRelatedByGroup(array $booking): array
    {
        $groupCode = (string) ($booking['booking_group_code'] ?? '');
        $userId = (int) ($booking['user_id'] ?? 0);
        $bookingId = (int) ($booking['id'] ?? 0);
        if ($groupCode === '' || $userId <= 0) {
            return [];
        }

        $stmt = $this->db()->prepare(
            'SELECT b.id, b.booking_code, b.direction, b.status, b.total_amount,
                    t.departure_time, t.arrival_time,
                    fl.name AS from_name, tl.name AS to_name,
                    GROUP_CONCAT(s.seat_number ORDER BY s.seat_number SEPARATOR ", ") AS seat_numbers
             FROM bookings b
             JOIN trips t ON t.id = b.trip_id
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             LEFT JOIN booking_details bd ON bd.booking_id = b.id
             LEFT JOIN seats s ON s.id = bd.seat_id
             WHERE b.booking_group_code = :group_code
               AND b.user_id = :user_id
               AND b.id <> :booking_id
             GROUP BY b.id, b.booking_code, b.direction, b.status, b.total_amount,
                      t.departure_time, t.arrival_time, fl.name, tl.name
             ORDER BY FIELD(b.direction, "outbound", "return"), t.departure_time ASC'
        );
        $stmt->execute([
            'group_code' => $groupCode,
            'user_id' => $userId,
            'booking_id' => $bookingId,
        ]);

        return $stmt->fetchAll();
    }

    public function canCancel(array $booking): bool
    {
        if (in_array((string) ($booking['status'] ?? ''), ['cancelled', 'completed', 'expired'], true)) {
            return false;
        }

        $departure = strtotime((string) ($booking['departure_time'] ?? ''));
        return $departure !== false && $departure > time();
    }

    public function cancelReason(array $booking): string
    {
        if (in_array((string) ($booking['status'] ?? ''), ['completed', 'expired'], true)) {
            return 'Vé không còn đủ điều kiện hủy.';
        }

        if (($booking['status'] ?? '') === 'cancelled') {
            return 'Vé đã được hủy.';
        }

        $departure = strtotime((string) ($booking['departure_time'] ?? ''));
        if ($departure === false) {
            return 'Không xác định được thời gian khởi hành.';
        }

        if ($departure <= time()) {
            return 'Không thể hủy vì chuyến xe đã khởi hành.';
        }

        return '';
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
