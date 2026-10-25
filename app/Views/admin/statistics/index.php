<div class="admin-page-header">
    <h1>Thống kê</h1>
    <a class="btn btn-outline-secondary" href="<?= url('/admin') ?>">Dashboard</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="row g-3">
    <div class="col-md-3"><div class="metric-card">Người dùng <strong><?= e($summary['users'] ?? 0) ?></strong></div></div>
    <div class="col-md-3"><div class="metric-card">Chuyến xe <strong><?= e($summary['trips'] ?? 0) ?></strong></div></div>
    <div class="col-md-3"><div class="metric-card">Đơn đặt vé <strong><?= e($summary['bookings'] ?? 0) ?></strong></div></div>
    <div class="col-md-3"><div class="metric-card">Doanh thu <strong><?= number_format((float) ($summary['revenue'] ?? 0)) ?> VND</strong></div></div>
</div>
