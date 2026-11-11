<?php
$booking = $booking ?? [];
$payment = $payment ?? [];
$method = (string) ($payment['method'] ?? '');
$amount = (float) ($payment['amount'] ?? $booking['total_amount'] ?? 0);
$methodLabels = [
    'momo' => 'MoMo',
    'zalopay' => 'ZaloPay',
    'bank_transfer' => 'Chuyển khoản',
    'card' => 'Thẻ ngân hàng',
];
$paymentQrDataUri = (string) ($paymentQrDataUri ?? '');
$payosData = isset($payosData) && is_array($payosData) ? $payosData : [];
$payosError = (string) ($payosError ?? '');
$checkoutUrl = (string) ($payosData['checkoutUrl'] ?? '');
?>

<style>
    .payment-box { background: #fff; border: 1px solid #e8edf3; border-radius: 8px; padding: 24px; box-shadow: 0 10px 30px rgba(15, 23, 42, .06); }
    .payment-summary { background: #f7fafc; border-radius: 8px; padding: 18px; }
    .real-qr { width: 260px; max-width: 100%; padding: 12px; border: 1px solid #d9e2ec; border-radius: 8px; margin: 0 auto; background: #fff; box-shadow: inset 0 0 0 6px #fff; }
    .real-qr img { display: block; width: 100%; height: auto; }
    .payment-note { color: #64748b; font-size: .95rem; }
    .payment-code { word-break: break-all; font-size: .85rem; color: #475569; }
</style>

<section class="container py-5">
    <?php if ($message = \App\Core\Session::getFlash('error')): ?>
        <div class="alert alert-danger"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($message = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <div class="payment-box">
                <span class="section-kicker">payOS</span>
                <h1 class="h3 mb-3"><?= e($methodLabels[$method] ?? 'Thanh toán') ?></h1>
                <p class="payment-note mb-4">
                    Tất cả phương thức thanh toán online đều dùng chung mã QR PayOS. Sau khi thanh toán, bấm kiểm tra để hệ thống xác nhận trạng thái PAID từ payOS.
                </p>

                <?php if ($payosError !== ''): ?>
                    <div class="alert alert-danger">
                        <?= e($payosError) ?>
                    </div>
                <?php endif; ?>

                <?php if ($paymentQrDataUri !== ''): ?>
                    <div class="text-center mb-4">
                        <div class="real-qr">
                            <img src="<?= e($paymentQrDataUri) ?>" alt="Mã QR payOS">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="payment-summary mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Mã đơn hàng payOS</span>
                        <strong><?= e((string) ($payosData['orderCode'] ?? '-')) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Trạng thái</span>
                        <strong><?= e((string) ($payosData['status'] ?? 'PENDING')) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Nội dung</span>
                        <strong><?= e((string) ($payosData['description'] ?? $booking['booking_code'] ?? '-')) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Số tiền</span>
                        <strong class="text-success"><?= number_format($amount, 0, ',', '.') ?>d</strong>
                    </div>
                    <?php if (!empty($payosData['qrCode'])): ?>
                        <hr>
                        <div class="payment-code"><?= e((string) $payosData['qrCode']) ?></div>
                    <?php endif; ?>
                </div>

                <?php if ($checkoutUrl !== ''): ?>
                    <a class="btn btn-outline-success w-100 mb-2" href="<?= e($checkoutUrl) ?>" target="_blank" rel="noopener">
                        Mở cổng thanh toán payOS
                    </a>
                <?php endif; ?>

                <form method="post" action="<?= url('/payment/confirm') ?>">
                    <input type="hidden" name="booking_id" value="<?= e($booking['id'] ?? '') ?>">
                    <input type="hidden" name="next_trip_id" value="<?= e($nextTripId ?? 0) ?>">
                    <input type="hidden" name="next_direction" value="<?= e($nextDirection ?? '') ?>">
                    <button class="btn btn-success w-100" type="submit">Kiểm tra thanh toán</button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="payment-box">
                <h2 class="h5 mb-3">Thông tin vé</h2>
                <dl class="row mb-0">
                    <dt class="col-5">Mã booking</dt>
                    <dd class="col-7"><?= e($booking['booking_code'] ?? '-') ?></dd>
                    <dt class="col-5">Tuyến xe</dt>
                    <dd class="col-7"><?= e(($booking['from_name'] ?? '-') . ' - ' . ($booking['to_name'] ?? '-')) ?></dd>
                    <dt class="col-5">Khởi hành</dt>
                    <dd class="col-7"><?= !empty($booking['departure_time']) ? e(date('H:i d/m/Y', strtotime((string) $booking['departure_time']))) : '-' ?></dd>
                    <dt class="col-5">Ghế</dt>
                    <dd class="col-7">
                        <?php $seatNumbers = array_column($booking['seats'] ?? [], 'seat_number'); ?>
                        <?= e($seatNumbers ? implode(', ', $seatNumbers) : '-') ?>
                    </dd>
                    <dt class="col-5">Phương thức</dt>
                    <dd class="col-7"><?= e($methodLabels[$method] ?? $method) ?></dd>
                    <dt class="col-5">Tổng tiền</dt>
                    <dd class="col-7 text-success fw-semibold"><?= number_format($amount, 0, ',', '.') ?>d</dd>
                </dl>
                <a class="btn btn-outline-secondary w-100 mt-3" href="<?= url('/booking/detail?id=' . (int) ($booking['id'] ?? 0)) ?>">Xem chi tiết vé</a>
            </div>
        </div>
    </div>
</section>
