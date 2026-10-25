<div class="admin-page-header">
    <h1>Đơn đặt vé <?= e($booking['booking_code']) ?></h1>
    <a class="btn btn-outline-secondary" href="<?= url('/admin/bookings') ?>">Quay lại</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="admin-card">
            <h2>Thông tin chuyến và khách hàng</h2>
            <dl class="row">
                <dt class="col-sm-3">Khách hàng</dt><dd class="col-sm-9"><?= e($booking['customer_name']) ?>, <?= e($booking['customer_phone']) ?>, <?= e($booking['customer_email']) ?></dd>
                <dt class="col-sm-3">Tuyến</dt><dd class="col-sm-9"><?= e($booking['from_name']) ?> -> <?= e($booking['to_name']) ?></dd>
                <dt class="col-sm-3">Xe</dt><dd class="col-sm-9"><?= e($booking['bus_name']) ?></dd>
                <dt class="col-sm-3">Thời gian</dt><dd class="col-sm-9"><?= e($booking['departure_time']) ?> -> <?= e($booking['arrival_time']) ?></dd>
                <dt class="col-sm-3">Tổng tiền</dt><dd class="col-sm-9"><?= number_format((float) $booking['total_amount']) ?> VND</dd>
            </dl>
            <h2>Ghế đã đặt</h2>
            <table class="table table-sm">
                <thead><tr><th>Ghế</th><th>Loại ghế</th><th>Giá vé</th></tr></thead>
                <tbody>
                <?php foreach ($booking['seats'] as $seat): ?>
                    <tr><td><?= e($seat['seat_number']) ?></td><td><?= e(admin_label($seat['seat_type'])) ?></td><td><?= number_format((float) $seat['price']) ?> VND</td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <form class="admin-card admin-form" method="post" action="<?= url('/admin/bookings/update-status') ?>">
            <h2>Trạng thái</h2>
            <input type="hidden" name="id" value="<?= e($booking['id']) ?>">
            <label class="form-label">Trạng thái đặt vé</label>
            <select class="form-select" name="status">
                <?php foreach (['pending', 'confirmed', 'cancelled', 'completed', 'expired'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= e(admin_label($status)) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary mt-3" type="submit">Cập nhật</button>
        </form>
        <div class="admin-card mt-3">
            <h2>Thanh toán</h2>
            <p><?= e(admin_label($booking['payment_method'] ?? '-')) ?> / <?= e(admin_label($booking['payment_status'] ?? '-')) ?></p>
            <p><?= e($booking['transaction_code'] ?? '') ?></p>
            <h2>Vé</h2>
            <p><?= e($booking['ticket_code'] ?? '-') ?> / <?= e(admin_label($booking['ticket_status'] ?? '-')) ?></p>
            <p><?= e($booking['qr_code_path'] ?? '') ?></p>
        </div>
    </div>
</div>
