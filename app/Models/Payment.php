<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Payment extends Model
{
    public function create(array $data): int { return 0; }
    public function updateStatus(int $id, string $status): bool { return false; }
}
