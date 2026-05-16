<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Bus extends Model
{
    public function all(): array
    {
        $stmt = $this->db()->query('SELECT * FROM buses ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM buses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $bus = $stmt->fetch();
        return $bus ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO buses (name, license_plate, bus_type, total_seats, image, status)
             VALUES (:name, :license_plate, :bus_type, :total_seats, :image, :status)'
        );
        $stmt->execute($this->payload($data));
        return (int) $this->db()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $payload = $this->payload($data);
        $payload['id'] = $id;
        $stmt = $this->db()->prepare(
            'UPDATE buses
             SET name = :name, license_plate = :license_plate, bus_type = :bus_type,
                 total_seats = :total_seats, image = :image, status = :status
             WHERE id = :id'
        );
        return $stmt->execute($payload);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db()->prepare('DELETE FROM buses WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function isUsed(int $id): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT
                (SELECT COUNT(*) FROM seats WHERE bus_id = :seat_bus_id) +
                (SELECT COUNT(*) FROM trips WHERE bus_id = :trip_bus_id) AS total'
        );
        $stmt->execute(['seat_bus_id' => $id, 'trip_bus_id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function payload(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'license_plate' => trim((string) ($data['license_plate'] ?? '')),
            'bus_type' => (string) ($data['bus_type'] ?? 'standard'),
            'total_seats' => (int) ($data['total_seats'] ?? 0),
            'image' => $this->nullable($data['image'] ?? null),
            'status' => (string) ($data['status'] ?? 'active'),
        ];
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
