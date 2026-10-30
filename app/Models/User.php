<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class User extends Model
{
    public function allWithRoles(): array
    {
        $stmt = $this->db()->query(
            'SELECT u.*, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             ORDER BY u.id DESC'
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT u.*, r.name AS role
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db()->prepare(
            'SELECT u.*, r.name AS role
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $passwordHash = (string) ($data['password_hash'] ?? '');
        if ($passwordHash === '') {
            $passwordHash = password_hash((string) ($data['password'] ?? ''), PASSWORD_DEFAULT);
        }

        $stmt = $this->db()->prepare(
            'INSERT INTO users (role_id, name, email, phone, password, status)
             VALUES (:role_id, :name, :email, :phone, :password, :status)'
        );
        $stmt->execute([
            'role_id' => (int) ($data['role_id'] ?? 2),
            'name' => trim((string) ($data['name'] ?? '')),
            'email' => strtolower(trim((string) ($data['email'] ?? ''))),
            'phone' => $this->nullable($data['phone'] ?? null),
            'password' => $passwordHash,
            'status' => (string) ($data['status'] ?? 'active'),
        ]);
        return (int) $this->db()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $params = [
            'id' => $id,
            'role_id' => (int) ($data['role_id'] ?? 2),
            'name' => trim((string) ($data['name'] ?? '')),
            'email' => strtolower(trim((string) ($data['email'] ?? ''))),
            'phone' => $this->nullable($data['phone'] ?? null),
            'status' => (string) ($data['status'] ?? 'active'),
        ];

        $password = trim((string) ($data['password'] ?? ''));
        if ($password !== '') {
            $params['password'] = password_hash($password, PASSWORD_DEFAULT);
            $sql = 'UPDATE users
                    SET role_id = :role_id, name = :name, email = :email, phone = :phone,
                        password = :password, status = :status
                    WHERE id = :id';
        } else {
            $sql = 'UPDATE users
                    SET role_id = :role_id, name = :name, email = :email,
                        phone = :phone, status = :status
                    WHERE id = :id';
        }

        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($params);
    }

    public function lock(int $id): bool
    {
        $stmt = $this->db()->prepare('UPDATE users SET status = "locked" WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function verifyLogin(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if (
            $user === null
            || ($user['status'] ?? '') !== 'active'
            || !password_verify($password, (string) $user['password'])
        ) {
            return null;
        }

        return $user;
    }

    public function updatePasswordHash(int $id, string $passwordHash): bool
    {
        $stmt = $this->db()->prepare('UPDATE users SET password = :password WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'password' => $passwordHash,
        ]);
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
