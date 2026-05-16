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
}
