<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Services\AuthService;
use InvalidArgumentException;
use Throwable;

final class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $user = $this->auth->login($email, (string) ($_POST['password'] ?? ''));

            if ($user === null) {
                http_response_code(422);
                $this->view('auth.login', [
                    'title' => 'Đăng nhập',
                    'error' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.',
                    'success' => null,
                    'old' => ['email' => $email],
                ]);
                return;
            }

            Auth::login($user);
            $this->redirect($this->intendedPath());
        }

        if (Auth::check() && !$this->hasIntendedPath()) {
            $this->redirect(Auth::isAdmin() ? '/admin' : '/');
        }

        $this->view('auth.login', [
            'title' => 'Đăng nhập',
            'error' => Session::getFlash('error'),
            'success' => Session::getFlash('success'),
            'old' => ['email' => ''],
        ]);
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->auth->register($_POST);
                Session::flash('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
                $this->redirect('/login');
            } catch (InvalidArgumentException $exception) {
                http_response_code(422);
                $this->view('auth.register', [
                    'title' => 'Đăng ký',
                    'error' => $exception->getMessage(),
                    'old' => $this->oldRegisterInput(),
                ]);
            } catch (Throwable) {
                http_response_code(500);
                $this->view('auth.register', [
                    'title' => 'Đăng ký',
                    'error' => 'Không thể đăng ký lúc này. Vui lòng thử lại sau.',
                    'old' => $this->oldRegisterInput(),
                ]);
            }
            return;
        }

        $this->view('auth.register', [
            'title' => 'Đăng ký',
            'error' => null,
            'old' => ['name' => '', 'email' => '', 'phone' => ''],
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . \url($path));
        exit;
    }

    private function intendedPath(): string
    {
        $path = (string) ($_GET['redirect'] ?? $_POST['redirect'] ?? '');
        if ($path === '' || !str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return Auth::isAdmin() ? '/admin' : '/';
        }

        if (str_starts_with($path, '/admin') && !Auth::isAdmin()) {
            return '/';
        }

        return $path;
    }

    private function hasIntendedPath(): bool
    {
        return (string) ($_GET['redirect'] ?? '') !== '';
    }

    private function oldRegisterInput(): array
    {
        return [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
        ];
    }
}
