<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class User extends Model
{
    public function findByEmail(string $email): ?array { return null; }
    public function create(array $data): int { return 0; }
    public function verifyLogin(string $email, string $password): bool { return false; }
}
