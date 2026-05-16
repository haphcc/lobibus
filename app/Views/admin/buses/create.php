<div class="admin-page-header">
    <h1>Thêm xe</h1>
    <a class="btn btn-outline-secondary" href="<?= url('/admin/buses') ?>">Quay lại</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<form class="admin-card admin-form" method="post" action="<?= url('/admin/buses/store') ?>">
    <?php $bus = ['bus_type' => 'standard', 'status' => 'active']; require __DIR__ . '/form.php'; ?>
    <button class="btn btn-primary" type="submit">Tạo mới</button>
</form>
