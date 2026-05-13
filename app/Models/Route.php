<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Route extends Model
{
    public function all(): array { return []; }
    public function find(int $id): ?array { return null; }
}
