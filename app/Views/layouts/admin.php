<?php $title = $title ?? 'Admin'; ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title) ?> |LobiBus Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body>
<div class="admin-shell">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <?= $content ?>
    </main>
</div>
<div class="admin-confirm-modal" data-admin-confirm-modal aria-hidden="true">
    <div class="admin-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="admin-confirm-title">
        <h2 id="admin-confirm-title">Xác nhận thao tác</h2>
        <p data-admin-confirm-message></p>
        <div class="admin-confirm-actions">
            <button class="btn btn-outline-secondary" type="button" data-admin-confirm-cancel>Hủy</button>
            <button class="btn btn-primary" type="button" data-admin-confirm-submit>Xác nhận</button>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.CSRF_TOKEN = '<?= e(csrf_token()) ?>';</script>
<script src="<?= asset('js/admin.js') ?>"></script>
<script src="<?= asset('js/dashboard-chart.js') ?>"></script>
</body>
</html>
