<div class="admin-page-header">
    <h1>Thêm người dùng</h1>
    <a class="btn btn-outline-secondary" href="<?= url('/admin/users') ?>">Quay lại</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<form class="admin-card admin-form" method="post" action="<?= url('/admin/users/store') ?>">
    <?php $user = ['status' => 'active', 'role_id' => 2]; $requirePassword = true; require __DIR__ . '/form.php'; ?>
    <button class="btn btn-primary" type="submit">Tạo mới</button>
</form>
