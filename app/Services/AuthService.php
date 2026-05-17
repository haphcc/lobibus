<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Role;
use App\Models\User;
use InvalidArgumentException;
use RuntimeException;

final class AuthService
{
    private User $users;
    private Role $roles;

    public function __construct(?User $users = null, ?Role $roles = null)
    {
        $this->users = $users ?? new User();
        $this->roles = $roles ?? new Role();
    }

    public function register(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $phone = trim((string) ($data['phone'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $passwordConfirmation = (string) ($data['password_confirmation'] ?? $password);

        if ($name === '') {
            throw new InvalidArgumentException('Vui lòng nhập họ tên.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email không hợp lệ.');
        }

        if (strlen($password) < 6) {
            throw new InvalidArgumentException('Mật khẩu phải có ít nhất 6 ký tự.');
        }

        if ($password !== $passwordConfirmation) {
            throw new InvalidArgumentException('Xác nhận mật khẩu không khớp.');
        }

        if ($this->users->findByEmail($email) !== null) {
            throw new InvalidArgumentException('Email này đã được đăng ký.');
        }

        $customerRole = $this->roles->findByName('customer');
        if ($customerRole === null) {
            throw new RuntimeException('Không tìm thấy vai trò khách hàng trong database.');
        }

        $userId = $this->users->create([
            'role_id' => (int) $customerRole['id'],
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'status' => 'active',
        ]);

        $user = $this->users->find($userId);
        if ($user === null) {
            throw new RuntimeException('Không thể tải lại tài khoản vừa tạo.');
        }

        return $this->users->findByEmail($email) ?? $user;
    }

    public function login(string $email, string $password): ?array
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            return null;
        }

        return $this->users->verifyLogin($email, $password);
    }
}
