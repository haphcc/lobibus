<?php
$booking = $booking ?? [];
$payment = $payment ?? [];
$banks = $banks ?? [];
$method = (string) ($payment['method'] ?? '');
$amount = (float) ($payment['amount'] ?? $booking['total_amount'] ?? 0);
$methodLabels = [
    'momo' => 'MoMo',
    'zalopay' => 'ZaloPay',
    'bank_transfer' => 'Chuyển khoản ngân hàng',
    'card' => 'Thẻ ngân hàng',
];
$isCard = $method === 'card';
$paymentQrDataUri = (string) ($paymentQrDataUri ?? '');
$qrSeed = substr(md5((string) ($booking['booking_code'] ?? '') . $method), 0, 49);
?>

<style>
    .payment-box { background: #fff; border: 1px solid #e8edf3; border-radius: 8px; padding: 24px; box-shadow: 0 10px 30px rgba(15, 23, 42, .06); }
    .payment-summary { background: #f7fafc; border-radius: 8px; padding: 18px; }
    .real-qr { width: 240px; max-width: 100%; padding: 12px; border: 1px solid #d9e2ec; border-radius: 8px; margin: 0 auto; background: #fff; box-shadow: inset 0 0 0 6px #fff; }
    .real-qr img { display: block; width: 100%; height: auto; }
    .fake-qr { width: 220px; height: 220px; display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; padding: 14px; border: 1px solid #d9e2ec; border-radius: 8px; margin: 0 auto; background: #fff; }
    .fake-qr span { border-radius: 3px; background: #111827; opacity: .12; }
    .fake-qr span.on { opacity: 1; }
    .payment-note { color: #64748b; font-size: .95rem; }
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
                <span class="section-kicker">Thanh toán</span>
                <h1 class="h3 mb-3"><?= e($methodLabels[$method] ?? 'Thanh toán đặt vé') ?></h1>
                <p class="payment-note mb-4">
                    Vui lòng hoàn tất thanh toán và bấm xác nhận để chuyển sang trang xác nhận vé.
                </p>

                <?php if (!$isCard): ?>
                    <div class="text-center mb-4">
                        <?php if ($paymentQrDataUri !== ''): ?>
                            <div class="real-qr">
                                <img src="<?= e($paymentQrDataUri) ?>" alt="Mã QR thanh toán">
                            </div>
                        <?php else: ?>
                            <div class="fake-qr" aria-label="Mã QR thanh toán">
                                <?php for ($i = 0; $i < 49; $i++): ?>
                                    <span class="<?= ((hexdec($qrSeed[$i] ?? '0') + $i) % 3 !== 0) ? 'on' : '' ?>"></span>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="payment-summary mb-4">
                        <?php if ($method === 'bank_transfer'): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ngân hàng nhận</span>
                                <strong>Vietcombank</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Số tài khoản</span>
                                <strong>9704 0000 1234 5678</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Chủ tài khoản</span>
                                <strong>CONG TY LOBIBUS</strong>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Nội dung</span>
                            <strong><?= e($booking['booking_code'] ?? '-') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Số tiền</span>
                            <strong class="text-success"><?= number_format($amount, 0, ',', '.') ?>đ</strong>
                        </div>
                    </div>

                    <form method="post" action="<?= url('/payment/confirm') ?>">
                        <input type="hidden" name="booking_id" value="<?= e($booking['id'] ?? '') ?>">
                        <input type="hidden" name="next_trip_id" value="<?= e($nextTripId ?? 0) ?>">
                        <input type="hidden" name="next_direction" value="<?= e($nextDirection ?? '') ?>">
                        <button class="btn btn-success w-100" type="submit">Xác nhận</button>
                    </form>
                <?php else: ?>
                    <form method="post" action="<?= url('/payment/confirm') ?>" class="row g-3">
                        <input type="hidden" name="booking_id" value="<?= e($booking['id'] ?? '') ?>">
                        <input type="hidden" name="next_trip_id" value="<?= e($nextTripId ?? 0) ?>">
                        <input type="hidden" name="next_direction" value="<?= e($nextDirection ?? '') ?>">

                        <div class="col-12">
                            <label class="form-label" for="bankCode">Ngân hàng</label>
                            <select id="bankCode" name="bank_code" class="form-select" required>
                                <option value="">Chọn ngân hàng</option>
                                <?php foreach ($banks as $code => $name): ?>
                                    <option value="<?= e($code) ?>"><?= e($name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="accountNumber">Số thẻ hoặc số tài khoản</label>
                            <input id="accountNumber" name="account_number" class="form-control" inputmode="numeric" placeholder="Ví dụ: 9704000012345678" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="accountHolder">Tên chủ thẻ/tài khoản</label>
                            <input id="accountHolder" name="account_holder" class="form-control" placeholder="NGUYEN VAN A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="expiry">Ngày hết hạn</label>
                            <input id="expiry" name="expiry" class="form-control" placeholder="MM/YY">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="cvv">CVV</label>
                            <input id="cvv" name="cvv" class="form-control" inputmode="numeric" maxlength="4" placeholder="123">
                        </div>
                        <div class="col-12">
                            <div class="payment-summary">
                                <div class="d-flex justify-content-between">
                                    <span>Số tiền thanh toán</span>
                                    <strong class="text-success"><?= number_format($amount, 0, ',', '.') ?>đ</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-success w-100" type="submit">Xác nhận thanh toán</button>
                        </div>
                    </form>
                <?php endif; ?>
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
                    <dd class="col-7 text-success fw-semibold"><?= number_format($amount, 0, ',', '.') ?>đ</dd>
                </dl>
                <a class="btn btn-outline-secondary w-100 mt-3" href="<?= url('/booking/detail?id=' . (int) ($booking['id'] ?? 0)) ?>">Xem chi tiết vé</a>
            </div>
        </div>
    </div>
</section>
