<?php
declare(strict_types=1);
namespace App\Services;

final class AuthService
{
    public function login(string $email, string $password): bool
    {
        // TODO: verify user from database with password_verify.
        return false;
    }
}
