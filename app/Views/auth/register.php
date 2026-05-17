<?php $old = $old ?? ['name' => '', 'email' => '', 'phone' => '']; ?>
<section class="container py-5" style="max-width:560px;">
    <h1 class="mb-4">Đăng ký</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= url('/register') ?>" class="card card-body shadow-sm">
        <label class="form-label" for="name">Họ tên</label>
        <input id="name" name="name" class="form-control mb-3" value="<?= e($old['name'] ?? '') ?>" required>

        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control mb-3" value="<?= e($old['email'] ?? '') ?>" required>

        <label class="form-label" for="phone">Số điện thoại</label>
        <input id="phone" name="phone" class="form-control mb-3" value="<?= e($old['phone'] ?? '') ?>">

        <label class="form-label" for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" class="form-control mb-3" minlength="6" required>

        <label class="form-label" for="password_confirmation">Xác nhận mật khẩu</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control mb-3" minlength="6" required>

        <button class="btn btn-success" type="submit">Tạo tài khoản</button>
        <div class="mt-3 d-flex justify-content-between gap-3">
            <a href="<?= url('/login') ?>">Đã có tài khoản</a>
            <a href="<?= url('/') ?>">Quay về trang chủ</a>
        </div>
    </form>
</section>
