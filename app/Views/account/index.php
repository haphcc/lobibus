<?php
$user = $user ?? [];
$profileOld = $profileOld ?? ['name' => $user['name'] ?? '', 'phone' => $user['phone'] ?? ''];
$otpPending = (bool) ($otpPending ?? false);
$otpRemainingSeconds = max(0, (int) ($otpRemainingSeconds ?? 0));
$otpRemainingText = sprintf('%02d:%02d', intdiv($otpRemainingSeconds, 60), $otpRemainingSeconds % 60);
?>
<section class="container py-5 account-page">
    <div class="account-heading">
        <div>
            <p class="account-eyebrow">Hồ sơ cá nhân</p>
            <h1>Tài khoản</h1>
        </div>
        <a class="account-back-link" href="<?= url('/') ?>">Quay về trang chủ</a>
    </div>

    <div class="account-grid">
        <form method="post" action="<?= url('/account/profile') ?>" class="account-card">
            <div class="account-card-header">
                <h2>Thông tin cá nhân</h2>
                <p>Email dùng để đăng nhập và không thay đổi tại đây.</p>
            </div>

            <?php if (!empty($profileError)): ?>
                <div class="alert alert-danger" role="alert"><?= e($profileError) ?></div>
            <?php endif; ?>

            <?php if (!empty($profileSuccess)): ?>
                <div class="alert alert-success" role="alert"><?= e($profileSuccess) ?></div>
            <?php endif; ?>

            <label class="form-label" for="account_name">Họ tên</label>
            <input id="account_name" name="name" class="form-control mb-3" value="<?= e($profileOld['name'] ?? '') ?>" required>

            <label class="form-label" for="account_email">Email</label>
            <input id="account_email" class="form-control mb-3" value="<?= e($user['email'] ?? '') ?>" disabled>

            <label class="form-label" for="account_phone">Số điện thoại</label>
            <input
                id="account_phone"
                name="phone"
                type="tel"
                inputmode="numeric"
                autocomplete="tel"
                class="form-control"
                value="<?= e($profileOld['phone'] ?? '') ?>"
                pattern="[0-9]{10}"
                maxlength="10"
                title="Số điện thoại phải gồm đúng 10 chữ số."
                required
            >
            <div class="form-text mb-3">Số điện thoại phải gồm đúng 10 chữ số.</div>

            <button class="btn btn-success account-submit" type="submit">Lưu thông tin</button>
        </form>

        <div class="account-card">
            <?php if (!empty($user['is_google'])): ?>
                <div class="account-card-header">
                    <h2>Mật khẩu & Bảo mật</h2>
                    <p>Tài khoản của bạn đang sử dụng hình thức xác thực thông qua Google.</p>
                </div>
                <div class="alert alert-info d-flex align-items-start gap-3 p-4" style="background-color: #ecfdf5; border: 1px solid #b9e6d1; border-radius: 12px; color: #0f766e;">
                    <div style="background: #ffffff; border-radius: 50%; padding: 0.6rem; display: inline-flex; box-shadow: 0 4px 10px rgba(0,0,0,0.05); flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" width="28" height="28" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                    </div>
                    <div>
                        <h4 style="font-weight: 700; font-size: 1.05rem; margin-top: 0; margin-bottom: 0.35rem; color: #0d6861;">Đăng nhập bằng Google</h4>
                        <p style="font-size: 0.92rem; line-height: 1.5; margin: 0; opacity: 0.95;">
                            Tài khoản của bạn được bảo mật bởi Google. Bạn không có mật khẩu riêng trên hệ thống LobiBus, do đó tính năng đổi mật khẩu trực tiếp tại đây đã được tắt để đảm bảo an toàn.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="account-card-header">
                    <h2>Đổi mật khẩu</h2>
                    <p>Nhập mật khẩu hiện tại và mật khẩu mới để thay đổi.</p>
                </div>

                <?php if (!empty($passwordError)): ?>
                    <div class="alert alert-danger" role="alert"><?= e($passwordError) ?></div>
                <?php endif; ?>

                <?php if (!empty($passwordSuccess)): ?>
                    <div class="alert alert-success" role="alert"><?= e($passwordSuccess) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= url('/account/password') ?>" class="account-password-form">
                    <label class="form-label" for="current_password">Mật khẩu hiện tại</label>
                    <input id="current_password" name="current_password" type="password" class="form-control mb-3" required>

                    <label class="form-label" for="new_password">Mật khẩu mới</label>
                    <input id="new_password" name="password" type="password" class="form-control" minlength="8" required>
                    <div class="form-text mb-3">Mật khẩu cần có ít nhất 8 ký tự.</div>

                    <label class="form-label" for="new_password_confirmation">Xác nhận mật khẩu mới</label>
                    <input id="new_password_confirmation" name="password_confirmation" type="password" class="form-control mb-3" minlength="8" required>

                    <button class="btn btn-success account-submit" type="submit">Cập nhật mật khẩu</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>
