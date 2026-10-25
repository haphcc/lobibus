<div class="admin-page-header">
    <h1>Đơn đặt vé</h1>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="admin-card">
    <table class="table table-striped align-middle">
        <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Tuyến</th><th>Giờ đi</th><th>Tổng tiền</th><th>Đặt vé</th><th>Thanh toán</th><th class="text-end">Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= e($booking['booking_code']) ?></td>
                <td><?= e($booking['customer_name']) ?><br><small><?= e($booking['customer_phone']) ?></small></td>
                <td><?= e($booking['from_name']) ?> -> <?= e($booking['to_name']) ?></td>
                <td><?= e($booking['departure_time']) ?></td>
                <td><?= number_format((float) $booking['total_amount']) ?> VND</td>
                <td><span class="badge text-bg-secondary"><?= e(admin_label($booking['status'])) ?></span></td>
                <td><?= e(admin_label($booking['payment_method'] ?? '-')) ?> / <?= e(admin_label($booking['payment_status'] ?? '-')) ?></td>
                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/bookings/detail?id=' . $booking['id']) ?>">Chi tiết</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
