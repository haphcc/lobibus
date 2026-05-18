<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function login(array $user): void
    {
        Session::set('user', self::sessionUser($user));
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::get('user') !== null;
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? null) === 'admin';
    }

    public static function isCustomer(): bool
    {
        return (self::user()['role'] ?? null) === 'customer';
    }

    public static function id(): ?int
    {
        $id = self::user()['id'] ?? null;
        return $id === null ? null : (int) $id;
    }

    private static function sessionUser(array $user): array
    {
        return [
            'id' => (int) ($user['id'] ?? 0),
            'role_id' => (int) ($user['role_id'] ?? 0),
            'role' => (string) ($user['role'] ?? $user['role_name'] ?? ''),
            'name' => (string) ($user['name'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'phone' => $user['phone'] ?? null,
            'status' => (string) ($user['status'] ?? ''),
        ];
    }
}
