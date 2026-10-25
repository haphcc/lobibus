<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Location extends Model
{
    public function all(): array
    {
        $stmt = $this->db()->query('SELECT * FROM locations ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM locations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $location = $stmt->fetch();
        return $location ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO locations (name, province, address, latitude, longitude)
             VALUES (:name, :province, :address, :latitude, :longitude)'
        );
        $stmt->execute($this->payload($data));
        return (int) $this->db()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $payload = $this->payload($data);
        $payload['id'] = $id;
        $stmt = $this->db()->prepare(
            'UPDATE locations
             SET name = :name, province = :province, address = :address,
                 latitude = :latitude, longitude = :longitude
             WHERE id = :id'
        );
        return $stmt->execute($payload);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db()->prepare('DELETE FROM locations WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function isUsed(int $id): bool
    {
        $stmt = $this->db()->prepare(
            'SELECT COUNT(*) FROM routes WHERE from_location_id = :from_id OR to_location_id = :to_id'
        );
        $stmt->execute(['from_id' => $id, 'to_id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function payload(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'province' => $this->nullable($data['province'] ?? null),
            'address' => $this->nullable($data['address'] ?? null),
            'latitude' => $this->nullable($data['latitude'] ?? null),
            'longitude' => $this->nullable($data['longitude'] ?? null),
        ];
    }

    private function nullable(mixed $value): mixed
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
