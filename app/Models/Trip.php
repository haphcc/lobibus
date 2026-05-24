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
                WHERE t.status = "scheduled"
                AND t.departure_time >= NOW()';
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
            if (!empty($filters['exact_date'])) {
                $sql .= ' AND DATE(t.departure_time) = :date';
            } else {
                $sql .= ' AND DATE(t.departure_time) >= :date';
            }
            $params['date'] = $filters['date'];
        }

        $sql .= ' GROUP BY t.id, b.name, b.total_seats, fl.name, tl.name, r.distance_km, r.duration_minutes ORDER BY t.departure_time ASC';

        if (!empty($filters['limit'])) {
            $limit = max(1, min(1500, (int) $filters['limit']));
            $sql .= ' LIMIT ' . $limit;
        }

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

    public function adminList(array $filters = [], int $limit = 25, int $offset = 0): array
    {
        $limit = max(1, min($limit, 100));
        $offset = max(0, $offset);
        [$where, $params] = $this->adminWhere($filters);

        $stmt = $this->db()->prepare(
            'SELECT t.*, b.name AS bus_name, fl.name AS from_name, tl.name AS to_name
             FROM trips t
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             JOIN buses b ON b.id = t.bus_id
             ' . $where . '
             ORDER BY t.departure_time DESC, t.id DESC
             LIMIT ' . $limit . ' OFFSET ' . $offset
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countForAdmin(array $filters = []): int
    {
        [$where, $params] = $this->adminWhere($filters);

        $stmt = $this->db()->prepare(
            'SELECT COUNT(*)
             FROM trips t
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             JOIN buses b ON b.id = t.bus_id
             ' . $where
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM trips WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $trip = $stmt->fetch();
        return $trip ?: null;
    }

    public function findWithDetails(int $id): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT t.*, b.name AS bus_name, b.bus_type, b.total_seats, b.image AS bus_image,
                    fl.name AS from_name, tl.name AS to_name,
                    fl.address AS from_address, tl.address AS to_address,
                    r.distance_km, r.duration_minutes
             FROM trips t
             JOIN routes r ON r.id = t.route_id
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             JOIN buses b ON b.id = t.bus_id
             WHERE t.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $trip = $stmt->fetch();
        return $trip ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO trips (route_id, bus_id, departure_time, arrival_time, price, available_seats, status)
             VALUES (:route_id, :bus_id, :departure_time, :arrival_time, :price, :available_seats, :status)'
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
                 arrival_time = :arrival_time, price = :price, available_seats = :available_seats, status = :status
             WHERE id = :id'
        );
        $updated = $stmt->execute($payload);
        $this->syncAvailableSeats($id);
        return $updated;
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

    public function decrementAvailableSeats(int $id, int $count): bool
    {
        $stmt = $this->db()->prepare(
            'UPDATE trips
             SET available_seats = available_seats - :count
             WHERE id = :id AND available_seats >= :count'
        );
        $stmt->execute(['id' => $id, 'count' => $count]);

        return $stmt->rowCount() === 1;
    }

    public function incrementAvailableSeats(int $id, int $count): bool
    {
        $stmt = $this->db()->prepare(
            'UPDATE trips t
             JOIN buses b ON b.id = t.bus_id
             SET t.available_seats = LEAST(t.available_seats + :count, b.total_seats)
             WHERE t.id = :id'
        );

        return $stmt->execute(['id' => $id, 'count' => $count]);
    }

    public function syncAvailableSeats(int $id): bool
    {
        $stmt = $this->db()->prepare(
            'UPDATE trips t
             JOIN buses bus ON bus.id = t.bus_id
             LEFT JOIN (
                 SELECT bd.trip_id, COUNT(DISTINCT bd.seat_id) AS booked_count
                 FROM booking_details bd
                 JOIN bookings bk ON bk.id = bd.booking_id
                 WHERE bk.status NOT IN ("cancelled", "expired")
                 GROUP BY bd.trip_id
             ) booked ON booked.trip_id = t.id
             SET t.available_seats = GREATEST(bus.total_seats - COALESCE(booked.booked_count, 0), 0)
             WHERE t.id = :id'
        );

        return $stmt->execute(['id' => $id]);
    }

    private function payload(array $data): array
    {
        $busId = (int) ($data['bus_id'] ?? 0);
        $availableSeats = array_key_exists('available_seats', $data)
            ? (int) $data['available_seats']
            : $this->totalSeatsForBus($busId);

        return [
            'route_id' => (int) ($data['route_id'] ?? 0),
            'bus_id' => $busId,
            'departure_time' => (string) ($data['departure_time'] ?? ''),
            'arrival_time' => (string) ($data['arrival_time'] ?? ''),
            'price' => (float) ($data['price'] ?? 0),
            'available_seats' => $availableSeats,
            'status' => (string) ($data['status'] ?? 'scheduled'),
        ];
    }

    private function adminWhere(array $filters): array
    {
        $where = [];
        $params = [];
        $query = trim((string) ($filters['q'] ?? ''));

        if ($query !== '') {
            if (preg_match('/^\d+$/', $query)) {
                $where[] = 't.id = :trip_id';
                $params['trip_id'] = (int) $query;
            } else {
                $where[] = '(fl.name LIKE :query OR tl.name LIKE :query OR b.name LIKE :query)';
                $params['query'] = '%' . $query . '%';
            }
        }

        return [$where === [] ? '' : 'WHERE ' . implode(' AND ', $where), $params];
    }

    private function totalSeatsForBus(int $busId): int
    {
        $stmt = $this->db()->prepare('SELECT total_seats FROM buses WHERE id = :id');
        $stmt->execute(['id' => $busId]);

        return (int) ($stmt->fetchColumn() ?: 0);
    }
}
