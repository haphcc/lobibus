<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class BookingDetail extends Model
{
    public function createDetail(int $bookingId, int $tripId, int $seatId, float $price): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO booking_details (booking_id, trip_id, seat_id, price)
             VALUES (:booking_id, :trip_id, :seat_id, :price)'
        );
        $stmt->execute([
            'booking_id' => $bookingId,
            'trip_id' => $tripId,
            'seat_id' => $seatId,
            'price' => $price,
        ]);

        return (int) $this->db()->lastInsertId();
    }

    public function createMany(int $bookingId, int $tripId, array $seats, float $price): bool
    {
        foreach ($seats as $seatId) {
            $this->createDetail($bookingId, $tripId, (int) $seatId, $price);
        }

        return true;
    }

    public function getDetailsByBookingId(int $bookingId): array
    {
        $stmt = $this->db()->prepare(
            'SELECT bd.*, s.seat_number, s.seat_type
             FROM booking_details bd
             JOIN seats s ON s.id = bd.seat_id
             WHERE bd.booking_id = :booking_id
             ORDER BY s.seat_number ASC'
        );
        $stmt->execute(['booking_id' => $bookingId]);

        return $stmt->fetchAll();
    }
}
