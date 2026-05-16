<?php
$statisticsHeading = 'Dashboard LobiBus';
$statisticsShowDashboardLink = false;
require dirname(__DIR__) . '/statistics/index.php';
?>

<div class="admin-card mt-4">
    <h2>Thao tác nhanh</h2>
    <div class="admin-actions">
        <a class="btn btn-primary" href="<?= url('/admin/trips/create') ?>">Thêm chuyến</a>
        <a class="btn btn-outline-primary" href="<?= url('/admin/bookings') ?>">Xem đơn đặt vé</a>
        <a class="btn btn-outline-primary" href="<?= url('/admin/payments') ?>">Cập nhật thanh toán</a>
    </div>
</div>
