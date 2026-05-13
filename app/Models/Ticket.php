<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Ticket extends Model
{
    public function create(array $data): int { return 0; }
    public function findByCode(string $code): ?array { return null; }
}
