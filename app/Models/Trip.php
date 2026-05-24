<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Trip extends Model
{
    public function search(array $filters = []): array
    {
        $sql = 'SELECT t.*, b.name AS bus_name, b.total_seats, b.image AS bus_image,
                       fl.name AS `from`, tl.name AS `to`,
                       fl.address AS from_address, fl.latitude AS from_lat, fl.longitude AS from_lng,
                       tl.address AS to_address, tl.latitude AS to_lat, tl.longitude AS to_lng,
                       r.distance_km, r.duration_minutes,
                       (b.total_seats - COUNT(booked.id)) AS available_seats
                FROM trips t
                JOIN routes r ON r.id = t.route_id
                JOIN locations fl ON fl.id = r.from_location_id
                JOIN locations tl ON tl.id = r.to_location_id
                JOIN buses b ON b.id = t.bus_id
                LEFT JOIN (
                    SELECT bd.id, bd.trip_id
                    FROM booking_details bd
                    JOIN bookings bk ON bk.id = bd.booking_id
                    WHERE bk.status NOT IN ("cancelled", "expired")
                ) booked ON booked.trip_id = t.id
                WHERE t.status = "scheduled"';
        $params = [];

        if (!empty($filters['from'])) {
            $sql .= ' AND fl.name LIKE :from';
            $params['from'] = '%' . $filters['from'] . '%';
        }
        if (!empty($filters['to'])) {
            $sql .= ' AND tl.name LIKE :to';
            $params['to'] = '%' . $filters['to'] . '%';
        }
        if (!empty($filters['date'])) {
            $sql .= ' AND DATE(t.departure_time) >= :date';
            $params['date'] = $filters['date'];
        }

        $sql .= ' GROUP BY t.id, b.name, b.total_seats, fl.name, tl.name, r.distance_km, r.duration_minutes ORDER BY t.departure_time ASC';
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function allWithDetails(): array
    {
        $stmt = $this->db()->query(
            'SELECT t.*, b.name AS bus_name, fl.name AS from_name, tl.name AS to_name
             FROM trips t
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             JOIN buses b ON b.id = t.bus_id
             ORDER BY t.departure_time DESC'
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM trips WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $trip = $stmt->fetch();
        return $trip ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO trips (route_id, bus_id, departure_time, arrival_time, price, status)
             VALUES (:route_id, :bus_id, :departure_time, :arrival_time, :price, :status)'
        );
        $stmt->execute($this->payload($data));
        return (int) $this->db()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $payload = $this->payload($data);
        $payload['id'] = $id;
        $stmt = $this->db()->prepare(
            'UPDATE trips
             SET route_id = :route_id, bus_id = :bus_id, departure_time = :departure_time,
                 arrival_time = :arrival_time, price = :price, status = :status
             WHERE id = :id'
        );
        return $stmt->execute($payload);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db()->prepare('DELETE FROM trips WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function isBooked(int $id): bool
    {
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM bookings WHERE trip_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getAvailableSeats(int $id): int
    {
        $stmt = $this->db()->prepare(
            'SELECT b.total_seats - COUNT(booked.id)
             FROM trips t
             JOIN buses b ON b.id = t.bus_id
             LEFT JOIN (
                 SELECT bd.id, bd.trip_id
                 FROM booking_details bd
                 JOIN bookings bk ON bk.id = bd.booking_id
                 WHERE bk.status NOT IN ("cancelled", "expired")
             ) booked ON booked.trip_id = t.id
             WHERE t.id = :id
             GROUP BY b.total_seats'
        );
        $stmt->execute(['id' => $id]);
        return (int) ($stmt->fetchColumn() ?: 0);
    }

    private function payload(array $data): array
    {
        return [
            'route_id' => (int) ($data['route_id'] ?? 0),
            'bus_id' => (int) ($data['bus_id'] ?? 0),
            'departure_time' => (string) ($data['departure_time'] ?? ''),
            'arrival_time' => (string) ($data['arrival_time'] ?? ''),
            'price' => (float) ($data['price'] ?? 0),
            'status' => (string) ($data['status'] ?? 'scheduled'),
        ];
    }
}
