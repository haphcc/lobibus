<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Services\AuthService;
use InvalidArgumentException;
use Throwable;

final class AccountController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function index(): void
    {
        $user = $this->requireUser();
        $otpRemainingSeconds = $this->auth->passwordOtpRemainingSeconds((int) $user['id']);

        $profileOld = Session::getFlash('profile_old', [
            'name' => $user['name'] ?? '',
            'phone' => $user['phone'] ?? '',
        ]);

        $this->view('account.index', [
            'title' => 'Tài khoản',
            'user' => $user,
            'profileOld' => $profileOld,
            'profileError' => Session::getFlash('profile_error'),
            'profileSuccess' => Session::getFlash('profile_success'),
            'passwordError' => Session::getFlash('password_error'),
            'passwordSuccess' => Session::getFlash('password_success'),
            'otpPending' => $otpRemainingSeconds > 0,
            'otpRemainingSeconds' => $otpRemainingSeconds,
        ]);
    }

    public function updateProfile(): void
    {
        $user = $this->requireUser();

        try {
            $updatedUser = $this->auth->updateProfile((int) $user['id'], $_POST);
            Auth::login($updatedUser);
            Session::flash('profile_success', 'Đã cập nhật thông tin tài khoản.');
        } catch (InvalidArgumentException $exception) {
            Session::flash('profile_error', $exception->getMessage());
            Session::flash('profile_old', $this->oldProfileInput());
        } catch (Throwable) {
            Session::flash('profile_error', 'Không thể cập nhật thông tin lúc này. Vui lòng thử lại sau.');
            Session::flash('profile_old', $this->oldProfileInput());
        }

        $this->redirect('/account');
    }

    public function requestPasswordOtp(): void
    {
        $user = $this->requireUser();

        if (!empty($user['is_google'])) {
            Session::flash('password_error', 'Tài khoản liên kết Google không cần sử dụng mã OTP đổi mật khẩu.');
            $this->redirect('/account');
        }

        try {
            $this->auth->requestPasswordChangeOtp((int) $user['id']);
            Session::flash('password_success', 'Mã OTP đã được gửi tới email tài khoản của bạn.');
        } catch (InvalidArgumentException $exception) {
            Session::flash('password_error', $exception->getMessage());
        } catch (Throwable) {
            Session::flash('password_error', 'Không thể gửi mã OTP lúc này. Vui lòng kiểm tra cấu hình email hoặc thử lại sau.');
        }

        $this->redirect('/account');
    }

    public function updatePassword(): void
    {
        $user = $this->requireUser();

        if (!empty($user['is_google'])) {
            Session::flash('password_error', 'Tài khoản liên kết Google không được phép đổi mật khẩu.');
            $this->redirect('/account');
        }

        try {
            $this->auth->changePassword(
                (int) $user['id'],
                (string) ($_POST['current_password'] ?? ''),
                (string) ($_POST['password'] ?? ''),
                (string) ($_POST['password_confirmation'] ?? '')
            );
            Session::flash('password_success', 'Đã cập nhật mật khẩu mới.');
        } catch (InvalidArgumentException $exception) {
            Session::flash('password_error', $exception->getMessage());
        } catch (Throwable) {
            Session::flash('password_error', 'Không thể cập nhật mật khẩu lúc này. Vui lòng thử lại sau.');
        }

        $this->redirect('/account');
    }

    private function requireUser(): array
    {
        if (!Auth::check()) {
            Session::flash('error', 'Bạn cần đăng nhập để quản lý tài khoản.');
            $this->redirect('/login?redirect=/account');
        }

        $user = $this->auth->findUser((int) Auth::id());
        if ($user === null || ($user['status'] ?? '') !== 'active') {
            Session::forget('user');
            Session::flash('error', 'Phiên đăng nhập không còn hợp lệ. Vui lòng đăng nhập lại.');
            $this->redirect('/login');
        }

        return $user;
    }

    private function oldProfileInput(): array
    {
        return [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
        ];
    }

    private function redirect(string $path): void
    {
        header('Location: ' . \url($path));
        exit;
    }
}
