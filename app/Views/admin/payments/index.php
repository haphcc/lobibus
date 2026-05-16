<div class="admin-page-header">
    <h1>Thanh toán</h1>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="admin-card">
    <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Đơn đặt vé</th><th>Khách hàng</th><th>Phương thức</th><th>Số tiền</th><th>Mã giao dịch</th><th>Trạng thái</th><th class="text-end">Cập nhật</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= e($payment['id']) ?></td>
                <td><?= e($payment['booking_code']) ?></td>
                <td><?= e($payment['customer_name']) ?><br><small><?= e($payment['customer_phone']) ?></small></td>
                <td><?= e(admin_label($payment['method'])) ?></td>
                <td><?= number_format((float) $payment['amount']) ?> VND</td>
                <td><?= e($payment['transaction_code']) ?></td>
                <td><span class="badge text-bg-secondary"><?= e(admin_label($payment['status'])) ?></span></td>
                <td class="text-end">
                    <form class="admin-inline-form justify-content-end" method="post" action="<?= url('/admin/payments/update-status') ?>">
                        <input type="hidden" name="id" value="<?= e($payment['id']) ?>">
                        <select class="form-select form-select-sm" name="status">
                            <?php foreach (['pending', 'paid', 'failed', 'refunded', 'cancelled'] as $status): ?>
                                <option value="<?= e($status) ?>" <?= $payment['status'] === $status ? 'selected' : '' ?>><?= e(admin_label($status)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-primary" type="submit">Lưu</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
