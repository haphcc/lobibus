<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Booking extends Model
{
    public function create(array $data): int { return 0; }
    public function findByUser(int $userId): array { return []; }
    public function cancel(int $id): bool { return false; }
}
