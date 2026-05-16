<section>
    <h1>Admin Dashboard</h1>
    <?php require dirname(__DIR__) . '/partials/messages.php'; ?>
    <div class="row g-3">
        <div class="col-md-3"><div class="metric-card">Người dùng <strong><?= e($summary['users'] ?? 0) ?></strong></div></div>
        <div class="col-md-3"><div class="metric-card">Chuyến xe <strong><?= e($summary['trips'] ?? 0) ?></strong></div></div>
        <div class="col-md-3"><div class="metric-card">Đơn đặt vé <strong><?= e($summary['bookings'] ?? 0) ?></strong></div></div>
        <div class="col-md-3"><div class="metric-card">Doanh thu <strong><?= number_format((float) ($summary['revenue'] ?? 0)) ?> VND</strong></div></div>
    </div>
    <div class="admin-card mt-4">
        <h2>Thao tác nhanh</h2>
        <div class="admin-actions">
            <a class="btn btn-primary" href="<?= url('/admin/trips/create') ?>">Thêm chuyến</a>
            <a class="btn btn-outline-primary" href="<?= url('/admin/bookings') ?>">Xem đơn đặt vé</a>
            <a class="btn btn-outline-primary" href="<?= url('/admin/payments') ?>">Cập nhật thanh toán</a>
        </div>
    </div>
</section>
