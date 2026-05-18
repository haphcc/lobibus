<?php require_once __DIR__ . '/labels.php'; ?>
<?php if (!empty($_GET['success'])): ?>
    <div class="admin-notice admin-notice-success" role="status">
        <strong>Thành công</strong>
        <span><?= e($_GET['success']) ?></span>
    </div>
<?php endif; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="admin-notice admin-notice-danger" role="alert">
        <strong>Cần kiểm tra</strong>
        <span><?= e($_GET['error']) ?></span>
    </div>
<?php endif; ?>
