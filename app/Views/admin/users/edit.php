<div class="admin-page-header">
    <h1>Sửa người dùng</h1>
    <a class="btn btn-outline-secondary" href="<?= url('/admin/users') ?>">Quay lại</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<form class="admin-card admin-form" method="post" action="<?= url('/admin/users/update') ?>">
    <input type="hidden" name="id" value="<?= e($user['id']) ?>">
    <?php $requirePassword = false; require __DIR__ . '/form.php'; ?>
    <button class="btn btn-primary" type="submit">Cập nhật</button>
</form>
