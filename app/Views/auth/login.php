<?php
$old = $old ?? ['email' => ''];
$redirect = (string) ($_GET['redirect'] ?? $_POST['redirect'] ?? '');
?>
<section class="container py-5 auth-page" style="max-width:520px;">
    <h1 class="mb-4">Đăng nhập</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= url('/login') ?>" class="card card-body shadow-sm auth-card">
        <?php if ($redirect !== ''): ?>
            <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <?php endif; ?>

        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control mb-3" value="<?= e($old['email'] ?? '') ?>" required>

        <label class="form-label" for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" class="form-control mb-3" required>

        <button class="btn btn-success" type="submit">Đăng nhập</button>
        <div class="auth-links mt-3 d-flex flex-wrap justify-content-between gap-3">
            <a href="<?= url('/register') ?>">Tạo tài khoản</a>
            <a href="<?= url('/forgot-password') ?>">Quên mật khẩu?</a>
            <a href="<?= url('/') ?>">Quay về trang chủ</a>
        </div>
    </form>
</section>
