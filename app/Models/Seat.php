<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Seat extends Model
{
    public function getSeatsByBus(int $busId): array
    {
        return $this->getByBus($busId);
    }

    public function getByBus(int $busId): array
    {
        $stmt = $this->db()->prepare('SELECT * FROM seats WHERE bus_id = :bus_id ORDER BY seat_number ASC');
        $stmt->execute(['bus_id' => $busId]);
        return $stmt->fetchAll();
    }

    public function getSeatStatusByTrip(int $tripId): array
    {
        return $this->getByTrip($tripId);
    }

    public function getByTrip(int $tripId): array
    {
        $stmt = $this->db()->prepare(
            'SELECT s.id AS seat_id, s.id, s.seat_number, s.seat_type, t.price,
                    b.bus_type, b.total_seats,
                    CASE WHEN booked.seat_id IS NULL THEN "available" ELSE "booked" END AS status
             FROM trips t
             JOIN buses b ON b.id = t.bus_id
             JOIN seats s ON s.bus_id = t.bus_id
             LEFT JOIN (
                 SELECT bd.trip_id, bd.seat_id
                 FROM booking_details bd
                 JOIN bookings b ON b.id = bd.booking_id
                 LEFT JOIN tickets tk ON tk.booking_id = b.id
                 WHERE b.status NOT IN ("cancelled", "expired")
                   AND (tk.id IS NULL OR tk.status <> "cancelled")
             ) booked ON booked.trip_id = t.id AND booked.seat_id = s.id
             WHERE t.id = :trip_id
             ORDER BY s.seat_number ASC'
        );
        $stmt->execute(['trip_id' => $tripId]);
        return $stmt->fetchAll();
    }

    public function getAvailableSeatsForTrip(int $tripId, array $seatIds): array
    {
        if ($seatIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
        $sql = "SELECT s.id, s.seat_number, s.seat_type, t.price
                FROM trips t
                JOIN seats s ON s.bus_id = t.bus_id
                WHERE t.id = ?
                  AND s.id IN ({$placeholders})
                  AND NOT EXISTS (
                      SELECT 1
                      FROM booking_details bd
                      JOIN bookings b ON b.id = bd.booking_id
                      LEFT JOIN tickets tk ON tk.booking_id = b.id
                      WHERE bd.trip_id = t.id
                        AND bd.seat_id = s.id
                        AND b.status NOT IN ('cancelled', 'expired')
                        AND (tk.id IS NULL OR tk.status <> 'cancelled')
                  )
                ORDER BY s.seat_number ASC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(array_merge([$tripId], array_map('intval', $seatIds)));

        return $stmt->fetchAll();
    }

    public function lockSeatsForTrip(int $tripId, array $seatIds): void
    {
        if ($seatIds === []) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
        $stmt = $this->db()->prepare(
            "SELECT s.id
             FROM trips t
             JOIN seats s ON s.bus_id = t.bus_id
             WHERE t.id = ? AND s.id IN ({$placeholders})
             FOR UPDATE"
        );
        $stmt->execute(array_merge([$tripId], array_map('intval', $seatIds)));
        $stmt->fetchAll();
    }

    public function isSeatAvailable(int $tripId, int $seatId): bool
    {
        return count($this->getAvailableSeatsForTrip($tripId, [$seatId])) === 1;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM seats WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $seat = $stmt->fetch();
        return $seat ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO seats (bus_id, seat_number, seat_type)
             VALUES (:bus_id, :seat_number, :seat_type)'
        );
        $stmt->execute([
            'bus_id' => (int) ($data['bus_id'] ?? 0),
            'seat_number' => trim((string) ($data['seat_number'] ?? '')),
            'seat_type' => (string) ($data['seat_type'] ?? 'standard'),
        ]);
        return (int) $this->db()->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db()->prepare('DELETE FROM seats WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function isBooked(int $id): bool
    {
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM booking_details WHERE seat_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
