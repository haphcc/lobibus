<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Services\AuthService;
use InvalidArgumentException;
use League\OAuth2\Client\Provider\Google;
use RuntimeException;
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

    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            $action = (string) ($_POST['action'] ?? 'send_temporary_password');

            try {
                if ($action === 'verify_temporary_password') {
                    $user = $this->auth->login($email, (string) ($_POST['temporary_password'] ?? ''));
                    if ($user === null) {
                        http_response_code(422);
                        $this->view('auth.forgot-password', [
                            'title' => 'Quên mật khẩu',
                            'error' => 'Mật khẩu tạm thời không đúng hoặc tài khoản đã bị khóa.',
                            'success' => null,
                            'old' => ['email' => $email],
                            'showTemporaryPasswordInput' => true,
                        ]);
                        return;
                    }

                    Auth::login($user);
                    $this->redirect(Auth::isAdmin() ? '/admin' : '/');
                }

                $this->auth->forgotPassword($email);
                $this->view('auth.forgot-password', [
                    'title' => 'Quên mật khẩu',
                    'error' => null,
                    'success' => 'Nếu email tồn tại và đang hoạt động, hệ thống đã gửi mật khẩu tạm thời cho bạn.',
                    'old' => ['email' => $email],
                    'showTemporaryPasswordInput' => true,
                ]);
            } catch (InvalidArgumentException $exception) {
                http_response_code(422);
                $this->view('auth.forgot-password', [
                    'title' => 'Quên mật khẩu',
                    'error' => $exception->getMessage(),
                    'success' => null,
                    'old' => ['email' => $email],
                    'showTemporaryPasswordInput' => false,
                ]);
            } catch (Throwable) {
                http_response_code(500);
                $this->view('auth.forgot-password', [
                    'title' => 'Quên mật khẩu',
                    'error' => 'Không thể gửi mật khẩu tạm thời lúc này. Vui lòng kiểm tra cấu hình email hoặc thử lại sau.',
                    'success' => null,
                    'old' => ['email' => $email],
                    'showTemporaryPasswordInput' => false,
                ]);
            }
            return;
        }

        $this->view('auth.forgot-password', [
            'title' => 'Quên mật khẩu',
            'error' => Session::getFlash('error'),
            'success' => Session::getFlash('success'),
            'old' => ['email' => ''],
            'showTemporaryPasswordInput' => false,
        ]);
    }

    public function googleRedirect(): void
    {
        try {
            $provider = $this->googleProvider();
            $authUrl = $provider->getAuthorizationUrl([
                'prompt' => 'select_account',
            ]);

            Session::set('google_oauth_state', $provider->getState());
            Session::set('google_oauth_redirect', $this->safeRedirectPath((string) ($_GET['redirect'] ?? '')));

            header('Location: ' . $authUrl);
            exit;
        } catch (InvalidArgumentException | RuntimeException $exception) {
            Session::flash('error', $exception->getMessage());
            $this->redirect('/login');
        } catch (Throwable) {
            Session::flash('error', 'Không thể bắt đầu đăng nhập bằng Google lúc này.');
            $this->redirect('/login');
        }
    }

    public function googleCallback(): void
    {
        try {
            if (!empty($_GET['error'])) {
                throw new InvalidArgumentException('Bạn đã hủy hoặc Google từ chối yêu cầu đăng nhập.');
            }

            $state = (string) ($_GET['state'] ?? '');
            $expectedState = (string) Session::get('google_oauth_state', '');
            Session::forget('google_oauth_state');

            if ($state === '' || $expectedState === '' || !hash_equals($expectedState, $state)) {
                throw new InvalidArgumentException('Phiên đăng nhập Google không hợp lệ. Vui lòng thử lại.');
            }

            $code = (string) ($_GET['code'] ?? '');
            if ($code === '') {
                throw new InvalidArgumentException('Google không trả về mã xác thực.');
            }

            $provider = $this->googleProvider();
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);
            $owner = $provider->getResourceOwner($token);
            $ownerData = method_exists($owner, 'toArray') ? $owner->toArray() : [];

            $email = strtolower(trim((string) ($ownerData['email'] ?? '')));
            $name = trim((string) ($ownerData['name'] ?? ''));
            if (($ownerData['email_verified'] ?? false) !== true) {
                throw new InvalidArgumentException('Google chưa xác minh email của tài khoản này.');
            }

            $user = $this->auth->loginOrCreateGoogleUser($email, $name);
            Auth::login($user);

            $redirect = (string) Session::get('google_oauth_redirect', '');
            Session::forget('google_oauth_redirect');
            $this->redirect($this->intendedPathFrom($redirect));
        } catch (InvalidArgumentException | RuntimeException $exception) {
            Session::flash('error', $exception->getMessage());
            $this->redirect('/login');
        } catch (Throwable) {
            Session::flash('error', 'Không thể đăng nhập bằng Google lúc này. Vui lòng thử lại sau.');
            $this->redirect('/login');
        }
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
        return $this->intendedPathFrom($path);
    }

    private function intendedPathFrom(string $path): string
    {
        if ($path === '' || !str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return Auth::isAdmin() ? '/admin' : '/';
        }

        if (str_starts_with($path, '/admin') && !Auth::isAdmin()) {
            return '/';
        }

        return $path;
    }

    private function safeRedirectPath(string $path): string
    {
        return $path !== '' && str_starts_with($path, '/') && !str_starts_with($path, '//') ? $path : '';
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

    private function googleProvider(): Google
    {
        $config = \config('google');
        $clientId = trim((string) ($config['client_id'] ?? ''));
        $clientSecret = trim((string) ($config['client_secret'] ?? ''));

        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('Chưa cấu hình Google Client ID hoặc Client Secret.');
        }

        return new Google([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $this->googleRedirectUri($config),
        ]);
    }

    private function googleRedirectUri(array $config): string
    {
        $redirectUri = trim((string) ($config['redirect_uri'] ?? ''));
        return $redirectUri !== '' ? $redirectUri : $this->absoluteUrl('/auth/google/callback');
    }

    private function absoluteUrl(string $path): string
    {
        $scheme = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443
        ) ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $scheme . '://' . $host . \url($path);
    }
}
