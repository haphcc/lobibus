<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

final class AuthController extends Controller
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: validate credentials with AuthService.
            Session::set('user', ['name' => $_POST['email'] ?? 'Demo user', 'role' => 'customer']);
            header('Location: /');
            return;
        }

        $this->view('auth.login', ['title' => 'Đăng nhập']);
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: create user with password_hash and redirect to login.
            header('Location: /login');
            return;
        }

        $this->view('auth.register', ['title' => 'Đăng ký']);
    }

    public function logout(): void
    {
        Session::destroy();
        header('Location: /');
    }
}
