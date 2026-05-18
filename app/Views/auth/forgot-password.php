<?php
$old = $old ?? ['email' => ''];
$showTemporaryPasswordInput = $showTemporaryPasswordInput ?? false;
?>
<section class="container py-5 auth-page" style="max-width:520px;">
    <h1 class="mb-4">Quên mật khẩu</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= url('/forgot-password') ?>" class="card card-body shadow-sm auth-card">
        <input type="hidden" name="action" value="send_temporary_password">

        <label class="form-label" for="email">Email tài khoản</label>
        <input id="email" name="email" type="email" class="form-control mb-3" value="<?= e($old['email'] ?? '') ?>" required>

        <button class="btn btn-success" type="submit">Gửi mật khẩu tạm thời</button>

        <?php if ($showTemporaryPasswordInput): ?>
            <div class="temporary-password-panel mt-4">
                <label class="form-label" for="temporary_password">Mật khẩu tạm thời từ email</label>
                <input
                    id="temporary_password"
                    name="temporary_password"
                    type="password"
                    class="form-control mb-3"
                    form="temporary-password-form"
                    autocomplete="one-time-code"
                    required
                >
                <button class="btn btn-outline-primary w-100" type="submit" form="temporary-password-form">
                    Xác nhận và đăng nhập
                </button>
            </div>
        <?php endif; ?>

        <div class="auth-links mt-3 d-flex flex-wrap justify-content-between gap-3">
            <a href="<?= url('/login') ?>">Quay lại đăng nhập</a>
            <a href="<?= url('/') ?>">Quay về trang chủ</a>
        </div>
    </form>

    <?php if ($showTemporaryPasswordInput): ?>
        <form id="temporary-password-form" method="post" action="<?= url('/forgot-password') ?>">
            <input type="hidden" name="action" value="verify_temporary_password">
            <input type="hidden" name="email" value="<?= e($old['email'] ?? '') ?>">
        </form>
    <?php endif; ?>
</section>
