<?php
$old = $old ?? ['name' => '', 'email' => '', 'phone' => ''];
$redirect = (string) ($_GET['redirect'] ?? $_POST['redirect'] ?? '');
?>
<section class="container py-5 auth-page" style="max-width:560px;">
    <h1 class="mb-4">Đăng ký</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= url('/register') ?>" class="card card-body shadow-sm auth-card">
        <?php if ($redirect !== ''): ?>
            <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <?php endif; ?>

        <a class="btn-google-auth" href="<?= url('/auth/google/redirect' . ($redirect !== '' ? '?redirect=' . rawurlencode($redirect) : '')) ?>">
            <svg class="google-icon" viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span>Đăng ký bằng Google</span>
        </a>

        <div class="auth-divider"><span>hoặc đăng ký bằng email</span></div>

        <label class="form-label" for="name">Họ tên</label>
        <input id="name" name="name" class="form-control mb-3" value="<?= e($old['name'] ?? '') ?>" required>

        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control mb-3" value="<?= e($old['email'] ?? '') ?>" required>

        <label class="form-label" for="phone">Số điện thoại</label>
        <input
            id="phone"
            name="phone"
            type="tel"
            inputmode="numeric"
            autocomplete="tel"
            class="form-control mb-3"
            value="<?= e($old['phone'] ?? '') ?>"
            pattern="[0-9]{10}"
            maxlength="10"
            title="Số điện thoại phải gồm đúng 10 chữ số."
            required
        >

        <label class="form-label" for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" class="form-control" minlength="8" required>
        <div class="form-text mb-3">Mật khẩu cần có ít nhất 8 ký tự.</div>

        <label class="form-label" for="password_confirmation">Xác nhận mật khẩu</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control mb-3" minlength="8" required>

        <button class="btn btn-success" type="submit">Tạo tài khoản</button>
        <div class="auth-links mt-3 d-flex flex-wrap justify-content-between gap-3">
            <a href="<?= url('/login') ?>">Đã có tài khoản</a>
            <a href="<?= url('/') ?>">Quay về trang chủ</a>
        </div>
    </form>
</section>
