<?php $old = $old ?? ['name' => '', 'email' => '', 'phone' => '']; ?>
<section class="container py-5 auth-page" style="max-width:560px;">
    <h1 class="mb-4">Đăng ký</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= url('/register') ?>" class="card card-body shadow-sm auth-card">
        <label class="form-label" for="name">Họ tên</label>
        <input id="name" name="name" class="form-control mb-3" value="<?= e($old['name'] ?? '') ?>" required>

        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control mb-3" value="<?= e($old['email'] ?? '') ?>" required>

        <label class="form-label" for="phone">Số điện thoại</label>
        <input
            id="phone"
            name="phone"
            type="tel"
            inputmode="tel"
            autocomplete="tel"
            class="form-control mb-3"
            value="<?= e($old['phone'] ?? '') ?>"
            pattern="(\+84|84|0)[ .-]?(3|5|7|8|9)([ .-]?[0-9]){8}"
            maxlength="18"
            title="Nhập số di động Việt Nam, ví dụ 0912345678 hoặc +84912345678."
            required
        >

        <label class="form-label" for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" class="form-control mb-3" minlength="8" required>

        <label class="form-label" for="password_confirmation">Xác nhận mật khẩu</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control mb-3" minlength="8" required>

        <button class="btn btn-success" type="submit">Tạo tài khoản</button>
        <div class="auth-links mt-3 d-flex flex-wrap justify-content-between gap-3">
            <a href="<?= url('/login') ?>">Đã có tài khoản</a>
            <a href="<?= url('/') ?>">Quay về trang chủ</a>
        </div>
    </form>
</section>
