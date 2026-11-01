<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Session;
use App\Models\Role;
use App\Models\User;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class AuthService
{
    private const PASSWORD_OTP_SESSION_KEY = 'account_password_otp';
    private const PASSWORD_OTP_TTL_SECONDS = 600;
    private const PASSWORD_OTP_MAX_ATTEMPTS = 5;

    private User $users;
    private Role $roles;
    private MailService $mail;

    public function __construct(?User $users = null, ?Role $roles = null, ?MailService $mail = null)
    {
        $this->users = $users ?? new User();
        $this->roles = $roles ?? new Role();
        $this->mail = $mail ?? new MailService();
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

        $phone = $this->normalizeVietnamesePhone($phone);

        $this->validatePasswordPolicy($password);

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

    public function findUser(int $id): ?array
    {
        return $this->users->find($id);
    }

    public function updateProfile(int $userId, array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        $phone = trim((string) ($data['phone'] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException('Vui lòng nhập họ tên.');
        }

        $phone = $this->normalizeVietnamesePhone($phone);

        if (!$this->users->updateProfile($userId, $name, $phone)) {
            throw new RuntimeException('Không thể cập nhật thông tin tài khoản.');
        }

        $user = $this->users->find($userId);
        if ($user === null) {
            throw new RuntimeException('Không thể tải lại tài khoản vừa cập nhật.');
        }

        return $user;
    }

    public function requestPasswordChangeOtp(int $userId): void
    {
        $user = $this->users->find($userId);
        if ($user === null || ($user['status'] ?? '') !== 'active') {
            throw new InvalidArgumentException('Tài khoản không tồn tại hoặc đã bị khóa.');
        }

        $email = (string) ($user['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email tài khoản không hợp lệ.');
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Session::set(self::PASSWORD_OTP_SESSION_KEY, [
            'user_id' => $userId,
            'hash' => password_hash($otp, PASSWORD_DEFAULT),
            'expires_at' => time() + self::PASSWORD_OTP_TTL_SECONDS,
            'attempts' => 0,
        ]);

        try {
            $sent = $this->mail->send(
                $email,
                'Mã OTP đổi mật khẩu LobiBus',
                $this->passwordChangeOtpEmailBody($user, $otp)
            );
        } catch (Throwable $exception) {
            Session::forget(self::PASSWORD_OTP_SESSION_KEY);
            throw $exception;
        }

        if (!$sent) {
            Session::forget(self::PASSWORD_OTP_SESSION_KEY);
            throw new RuntimeException('Không thể gửi mã OTP đổi mật khẩu.');
        }
    }

    public function changePasswordWithOtp(int $userId, string $otp, string $password, string $passwordConfirmation): void
    {
        $this->validatePasswordPolicy($password);

        if ($password !== $passwordConfirmation) {
            throw new InvalidArgumentException('Xác nhận mật khẩu không khớp.');
        }

        $otpData = Session::get(self::PASSWORD_OTP_SESSION_KEY);
        if (!is_array($otpData) || (int) ($otpData['user_id'] ?? 0) !== $userId) {
            throw new InvalidArgumentException('Vui lòng gửi mã OTP trước khi đổi mật khẩu.');
        }

        if ((int) ($otpData['expires_at'] ?? 0) < time()) {
            Session::forget(self::PASSWORD_OTP_SESSION_KEY);
            throw new InvalidArgumentException('Mã OTP đã hết hạn. Vui lòng gửi mã mới.');
        }

        $attempts = (int) ($otpData['attempts'] ?? 0);
        if ($attempts >= self::PASSWORD_OTP_MAX_ATTEMPTS) {
            Session::forget(self::PASSWORD_OTP_SESSION_KEY);
            throw new InvalidArgumentException('Bạn đã nhập sai OTP quá nhiều lần. Vui lòng gửi mã mới.');
        }

        $otp = trim($otp);
        if (!preg_match('/^[0-9]{6}$/', $otp) || !password_verify($otp, (string) ($otpData['hash'] ?? ''))) {
            $otpData['attempts'] = $attempts + 1;
            if ($otpData['attempts'] >= self::PASSWORD_OTP_MAX_ATTEMPTS) {
                Session::forget(self::PASSWORD_OTP_SESSION_KEY);
            } else {
                Session::set(self::PASSWORD_OTP_SESSION_KEY, $otpData);
            }

            throw new InvalidArgumentException('Mã OTP không đúng.');
        }

        if (!$this->users->updatePasswordHash($userId, password_hash($password, PASSWORD_DEFAULT))) {
            throw new RuntimeException('Không thể cập nhật mật khẩu mới.');
        }

        Session::forget(self::PASSWORD_OTP_SESSION_KEY);
    }

    public function hasActivePasswordOtp(int $userId): bool
    {
        return $this->passwordOtpRemainingSeconds($userId) > 0;
    }

    public function passwordOtpRemainingSeconds(int $userId): int
    {
        $otpData = Session::get(self::PASSWORD_OTP_SESSION_KEY);
        if (!is_array($otpData) || (int) ($otpData['user_id'] ?? 0) !== $userId) {
            return 0;
        }

        $remainingSeconds = (int) ($otpData['expires_at'] ?? 0) - time();
        if ($remainingSeconds <= 0) {
            Session::forget(self::PASSWORD_OTP_SESSION_KEY);
            return 0;
        }

        return $remainingSeconds;
    }

    public function loginOrCreateGoogleUser(string $email, string $name): array
    {
        $email = strtolower(trim($email));
        $name = trim($name);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Google không trả về email hợp lệ.');
        }

        $user = $this->users->findByEmail($email);
        if ($user !== null) {
            if (($user['status'] ?? '') !== 'active') {
                throw new InvalidArgumentException('Tài khoản này đã bị khóa.');
            }

            return $user;
        }

        $customerRole = $this->roles->findByName('customer');
        if ($customerRole === null) {
            throw new RuntimeException('Không tìm thấy vai trò khách hàng trong database.');
        }

        if ($name === '') {
            $name = strstr($email, '@', true) ?: $email;
        }

        $userId = $this->users->create([
            'role_id' => (int) $customerRole['id'],
            'name' => $name,
            'email' => $email,
            'phone' => null,
            'password_hash' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
            'status' => 'active',
        ]);

        $user = $this->users->find($userId);
        if ($user === null) {
            throw new RuntimeException('Không thể tải lại tài khoản Google vừa tạo.');
        }

        return $user;
    }

    private function normalizeVietnamesePhone(string $phone): string
    {
        $phone = trim($phone);

        if ($phone === '') {
            throw new InvalidArgumentException('Vui lòng nhập số điện thoại.');
        }

        if (!preg_match('/^\+?[0-9\s.\-()]+$/', $phone)) {
            throw new InvalidArgumentException('Số điện thoại chỉ được gồm chữ số, dấu cách, dấu chấm, dấu gạch ngang hoặc mã quốc gia +84.');
        }

        $phone = preg_replace('/[\s.\-()]+/', '', $phone) ?? $phone;

        if (str_starts_with($phone, '+84')) {
            $phone = '0' . substr($phone, 3);
        } elseif (str_starts_with($phone, '84')) {
            $phone = '0' . substr($phone, 2);
        }

        if (!preg_match('/^0[0-9]{9}$/', $phone)) {
            throw new InvalidArgumentException('Số điện thoại phải có 10 chữ số, ví dụ 0912345678 hoặc +84912345678.');
        }

        if (!preg_match('/^0(3|5|7|8|9)[0-9]{8}$/', $phone)) {
            throw new InvalidArgumentException('Số điện thoại phải là số di động Việt Nam, bắt đầu bằng 03, 05, 07, 08 hoặc 09.');
        }

        return $phone;
    }

    public function forgotPassword(string $email): bool
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email không hợp lệ.');
        }

        $user = $this->users->findByEmail($email);
        if ($user === null || ($user['status'] ?? '') !== 'active') {
            return false;
        }

        if (($user['role'] ?? '') === 'admin') {
            throw new InvalidArgumentException('Tài khoản quản trị viên không được phép khôi phục mật khẩu trực tuyến để bảo mật.');
        }

        $temporaryPassword = $this->generateTemporaryPassword();
        $newHash = password_hash($temporaryPassword, PASSWORD_DEFAULT);
        $oldHash = (string) ($user['password'] ?? '');

        if (!$this->users->updatePasswordHash((int) $user['id'], $newHash)) {
            throw new RuntimeException('Không thể cập nhật mật khẩu tạm thời.');
        }

        $sent = $this->mail->send(
            (string) $user['email'],
            'Mật khẩu tạm thời LobiBus',
            $this->temporaryPasswordEmailBody($user, $temporaryPassword)
        );

        if (!$sent) {
            if ($oldHash !== '') {
                $this->users->updatePasswordHash((int) $user['id'], $oldHash);
            }

            throw new RuntimeException('Không thể gửi email mật khẩu tạm thời.');
        }

        return true;
    }

    private function validatePasswordPolicy(string $password): void
    {
        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Mật khẩu phải có ít nhất 8 ký tự.');
        }
    }

    private function generateTemporaryPassword(): string
    {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $digits = '23456789';
        $special = '!@#$%^&*';
        $all = $upper . $lower . $digits . $special;

        $chars = [
            $upper[random_int(0, strlen($upper) - 1)],
            $lower[random_int(0, strlen($lower) - 1)],
            $digits[random_int(0, strlen($digits) - 1)],
            $special[random_int(0, strlen($special) - 1)],
        ];

        while (count($chars) < 12) {
            $chars[] = $all[random_int(0, strlen($all) - 1)];
        }

        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return implode('', $chars);
    }

    private function temporaryPasswordEmailBody(array $user, string $temporaryPassword): string
    {
        $name = trim((string) ($user['name'] ?? ''));
        $displayName = $name !== '' ? \e($name) : 'quý khách';
        $password = \e($temporaryPassword);
        $loginUrl = \e('localhost/lobibus/public/login');
        $year = date('Y');

        return <<<HTML
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Mật khẩu tạm thời LobiBus</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f6;font-family:Arial,Helvetica,sans-serif;color:#18352d;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7f6;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border:1px solid #dcebe4;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f766e;padding:22px 28px;color:#ffffff;">
                            <div style="font-size:22px;font-weight:800;letter-spacing:.2px;">LobiBus</div>
                            <div style="font-size:14px;opacity:.9;margin-top:4px;">Yêu cầu khôi phục mật khẩu</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="font-size:16px;line-height:1.6;margin:0 0 16px;">Xin chào {$displayName},</p>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 18px;">
                                Chúng tôi đã nhận được yêu cầu cấp mật khẩu tạm thời cho tài khoản LobiBus của bạn.
                                Vui lòng sử dụng mật khẩu dưới đây để đăng nhập.
                            </p>
                            <div style="background:#ecfdf5;border:1px solid #b9e6d1;border-radius:8px;padding:18px;text-align:center;margin:22px 0;">
                                <div style="font-size:13px;font-weight:700;color:#0f766e;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Mật khẩu tạm thời</div>
                                <div style="font-family:'Courier New',monospace;font-size:28px;font-weight:800;color:#18352d;letter-spacing:1px;">{$password}</div>
                            </div>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 18px;">
                                Vì lý do bảo mật, hãy đăng nhập bằng mật khẩu tạm thời này và đổi sang mật khẩu mới ngay sau đó.
                            </p>
                            <p style="margin:0 0 22px;">
                                <a href="{$loginUrl}" style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:700;padding:11px 18px;border-radius:8px;">Đăng nhập LobiBus</a>
                            </p>
                            <p style="font-size:13px;line-height:1.6;color:#6b7f77;margin:0;">
                                Nếu bạn không yêu cầu khôi phục mật khẩu, vui lòng bỏ qua email này hoặc liên hệ bộ phận hỗ trợ LobiBus để được kiểm tra tài khoản.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f7fbf9;border-top:1px solid #e4efea;padding:16px 28px;color:#6b7f77;font-size:12px;line-height:1.5;">
                            Email này được gửi tự động từ hệ thống LobiBus. Vui lòng không trả lời trực tiếp email này.<br>
                            &copy; {$year} LobiBus. Đã đăng ký bản quyền.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function passwordChangeOtpEmailBody(array $user, string $otp): string
    {
        $name = trim((string) ($user['name'] ?? ''));
        $displayName = $name !== '' ? \e($name) : 'quý khách';
        $safeOtp = \e($otp);
        $year = date('Y');

        return <<<HTML
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Mã OTP đổi mật khẩu LobiBus</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f6;font-family:Arial,Helvetica,sans-serif;color:#18352d;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7f6;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border:1px solid #dcebe4;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f766e;padding:22px 28px;color:#ffffff;">
                            <div style="font-size:22px;font-weight:800;">LobiBus</div>
                            <div style="font-size:14px;opacity:.9;margin-top:4px;">Xác nhận đổi mật khẩu</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="font-size:16px;line-height:1.6;margin:0 0 16px;">Xin chào {$displayName},</p>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 18px;">
                                Bạn vừa yêu cầu đổi mật khẩu tài khoản LobiBus. Vui lòng nhập mã OTP dưới đây trên trang tài khoản.
                            </p>
                            <div style="background:#ecfdf5;border:1px solid #b9e6d1;border-radius:8px;padding:18px;text-align:center;margin:22px 0;">
                                <div style="font-size:13px;font-weight:700;color:#0f766e;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Mã OTP</div>
                                <div style="font-family:'Courier New',monospace;font-size:32px;font-weight:800;color:#18352d;letter-spacing:4px;">{$safeOtp}</div>
                            </div>
                            <p style="font-size:14px;line-height:1.7;color:#6b7f77;margin:0;">
                                Mã này có hiệu lực trong 10 phút. Nếu bạn không yêu cầu đổi mật khẩu, vui lòng bỏ qua email này.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f7fbf9;border-top:1px solid #e4efea;padding:16px 28px;color:#6b7f77;font-size:12px;line-height:1.5;">
                            Email này được gửi tự động từ hệ thống LobiBus. Vui lòng không trả lời trực tiếp email này.<br>
                            &copy; {$year} LobiBus. Đã đăng ký bản quyền.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
