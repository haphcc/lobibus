<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Seat extends Model
{
    public function getByBus(int $busId): array
    {
        $stmt = $this->db()->prepare('SELECT * FROM seats WHERE bus_id = :bus_id ORDER BY seat_number ASC');
        $stmt->execute(['bus_id' => $busId]);
        return $stmt->fetchAll();
    }

    public function getByTrip(int $tripId): array
    {
        $stmt = $this->db()->prepare(
            'SELECT s.*,
                    CASE WHEN booked.id IS NULL THEN "available" ELSE "booked" END AS status
             FROM trips t
             JOIN seats s ON s.bus_id = t.bus_id
             LEFT JOIN (
                 SELECT bd.id, bd.trip_id, bd.seat_id
                 FROM booking_details bd
                 JOIN bookings b ON b.id = bd.booking_id
                 WHERE b.status NOT IN ("cancelled", "expired")
             ) booked ON booked.trip_id = t.id AND booked.seat_id = s.id
             WHERE t.id = :trip_id
             ORDER BY s.seat_number ASC'
        );
        $stmt->execute(['trip_id' => $tripId]);
        return $stmt->fetchAll();
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
