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
                inputmode="tel"
                autocomplete="tel"
                class="form-control"
                value="<?= e($profileOld['phone'] ?? '') ?>"
                pattern="(\+84|84|0)[ .-]?(3|5|7|8|9)([ .-]?[0-9]){8}"
                maxlength="18"
                title="Nhập số di động Việt Nam, ví dụ 0912345678 hoặc +84912345678."
                required
            >
            <div class="form-text mb-3">Số di động Việt Nam bắt đầu bằng 03, 05, 07, 08, 09 hoặc dùng mã quốc gia +84.</div>

            <button class="btn btn-success account-submit" type="submit">Lưu thông tin</button>
        </form>

        <div class="account-card">
            <div class="account-card-header">
                <h2>Đổi mật khẩu</h2>
                <p>Gửi OTP về email tài khoản rồi nhập mã để đặt mật khẩu mới.</p>
            </div>

            <?php if (!empty($passwordError)): ?>
                <div class="alert alert-danger" role="alert"><?= e($passwordError) ?></div>
            <?php endif; ?>

            <?php if (!empty($passwordSuccess)): ?>
                <div class="alert alert-success" role="alert"><?= e($passwordSuccess) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= url('/account/password/request-otp') ?>" class="account-otp-form">
                <button class="btn btn-outline-primary account-submit" type="submit">
                    <?= $otpPending ? 'Gửi lại mã OTP' : 'Gửi mã OTP' ?>
                </button>
            </form>

            <?php if ($otpPending): ?>
                <div class="account-otp-note" data-otp-note aria-live="polite">
                    OTP đã được gửi. Mã còn hiệu lực trong
                    <span
                        class="account-otp-countdown"
                        data-otp-countdown
                        data-remaining-seconds="<?= e($otpRemainingSeconds) ?>"
                    ><?= e($otpRemainingText) ?></span>.
                </div>
            <?php endif; ?>

            <form method="post" action="<?= url('/account/password') ?>" class="account-password-form">
                <label class="form-label" for="otp">Mã OTP</label>
                <input id="otp" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" class="form-control mb-3" required>

                <label class="form-label" for="new_password">Mật khẩu mới</label>
                <input id="new_password" name="password" type="password" class="form-control" minlength="8" required>
                <div class="form-text mb-3">Mật khẩu cần có ít nhất 8 ký tự.</div>

                <label class="form-label" for="new_password_confirmation">Xác nhận mật khẩu mới</label>
                <input id="new_password_confirmation" name="password_confirmation" type="password" class="form-control mb-3" minlength="8" required>

                <button class="btn btn-success account-submit" type="submit" data-password-submit>Cập nhật mật khẩu</button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const countdown = document.querySelector('[data-otp-countdown]');
    if (!countdown) {
        return;
    }

    const note = document.querySelector('[data-otp-note]');
    const passwordSubmit = document.querySelector('[data-password-submit]');
    let remaining = Number.parseInt(countdown.dataset.remainingSeconds || '0', 10);

    function formatTime(totalSeconds) {
        const minutes = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
        const seconds = (totalSeconds % 60).toString().padStart(2, '0');
        return `${minutes}:${seconds}`;
    }

    function renderCountdown() {
        if (remaining <= 0) {
            countdown.textContent = '00:00';

            if (note) {
                note.classList.add('is-expired');
                note.textContent = 'OTP đã hết hạn. Vui lòng gửi mã mới để đổi mật khẩu.';
            }

            if (passwordSubmit) {
                passwordSubmit.disabled = true;
            }

            return false;
        }

        countdown.textContent = formatTime(remaining);
        return true;
    }

    if (!renderCountdown()) {
        return;
    }

    const timer = window.setInterval(function () {
        remaining -= 1;
        if (!renderCountdown()) {
            window.clearInterval(timer);
        }
    }, 1000);
});
</script>
