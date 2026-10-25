<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Route extends Model
{
    public function all(): array
    {
        return $this->allWithLocations();
    }

    public function allWithLocations(): array
    {
        $stmt = $this->db()->query(
            'SELECT r.*, fl.name AS from_name, tl.name AS to_name
             FROM routes r
             JOIN locations fl ON fl.id = r.from_location_id
             JOIN locations tl ON tl.id = r.to_location_id
             ORDER BY r.id DESC'
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM routes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $route = $stmt->fetch();
        return $route ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO routes (from_location_id, to_location_id, distance_km, duration_minutes, status)
             VALUES (:from_location_id, :to_location_id, :distance_km, :duration_minutes, :status)'
        );
        $stmt->execute($this->payload($data));
        return (int) $this->db()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $payload = $this->payload($data);
        $payload['id'] = $id;
        $stmt = $this->db()->prepare(
            'UPDATE routes
             SET from_location_id = :from_location_id, to_location_id = :to_location_id,
                 distance_km = :distance_km, duration_minutes = :duration_minutes, status = :status
             WHERE id = :id'
        );
        return $stmt->execute($payload);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db()->prepare('DELETE FROM routes WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function isUsed(int $id): bool
    {
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM trips WHERE route_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function payload(array $data): array
    {
        return [
            'from_location_id' => (int) ($data['from_location_id'] ?? 0),
            'to_location_id' => (int) ($data['to_location_id'] ?? 0),
            'distance_km' => $this->nullable($data['distance_km'] ?? null),
            'duration_minutes' => $this->nullable($data['duration_minutes'] ?? null),
            'status' => (string) ($data['status'] ?? 'active'),
        ];
    }

    private function nullable(mixed $value): mixed
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
