<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Role extends Model
{
    public function all(): array
    {
        $stmt = $this->db()->query('SELECT * FROM roles ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM roles WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => trim($name)]);
        $role = $stmt->fetch();
        return $role ?: null;
    }
}
