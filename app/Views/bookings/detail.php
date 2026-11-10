<?php $pageJs = ['booking.js']; ?>
<?php $booking = $booking ?? null; ?>
<?php
$isRoundTrip = $booking && (($booking['trip_type'] ?? 'oneway') === 'roundtrip');
$directionLabel = $booking && (($booking['direction'] ?? 'outbound') === 'return') ? 'Chiều về' : 'Chiều đi';
?>
<section class="booking-page py-5">
    <div class="container">
        <?php if ($message = \App\Core\Session::getFlash('success')): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($message = \App\Core\Session::getFlash('error')): ?>
            <div class="alert alert-danger"><?= e($message) ?></div>
        <?php endif; ?>

        <?php if (!$booking): ?>
            <div class="booking-panel">
                <h1 class="h4">Không tìm thấy booking</h1>
                <p class="text-muted"><?= e($message ?? 'Booking không tồn tại.') ?></p>
                <a class="btn btn-success" href="<?= url('/booking/history') ?>">Về lịch sử đặt vé</a>
            </div>
        <?php else: ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div>
                    <span class="section-kicker">Booking</span>
                    <h1 class="h3 mb-1"><?= e($booking['booking_code']) ?></h1>
                    <?php if ($isRoundTrip): ?>
                        <div class="mb-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                Vé khứ hồi · <?= e($directionLabel) ?> · <?= e($booking['booking_group_code'] ?? '') ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <span class="booking-status <?= e($booking['status']) ?>"><?= e($booking['status']) ?></span>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary" href="<?= url('/booking/history') ?>">Lịch sử</a>
                    <?php if (!empty($booking['can_cancel'])): ?>
                        <form class="js-cancel-booking-form" method="post" action="<?= url('/booking/cancel') ?>">
                            <input type="hidden" name="booking_id" value="<?= e($booking['id']) ?>">
                            <button class="btn btn-outline-danger" type="submit">Hủy vé</button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-outline-danger" type="button" disabled title="<?= e($booking['cancel_reason'] ?? 'Không thể hủy vé này.') ?>">
                            Không thể hủy
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="booking-panel mb-4">
                        <h2 class="h5 mb-3">Thông tin hành trình</h2>
                        <div class="booking-route-box mb-4">
                            <div>
                                <small>Điểm đi</small>
                                <strong><?= e($booking['from_name']) ?></strong>
                            </div>
                            <i class="bi bi-arrow-right"></i>
                            <div>
                                <small>Điểm đến</small>
                                <strong><?= e($booking['to_name']) ?></strong>
                            </div>
                        </div>
                        <dl class="booking-info-list two-columns">
                            <dt>Xe</dt>
                            <dd><?= e($booking['bus_name']) ?></dd>
                            <dt>Khởi hành</dt>
                            <dd><?= e(date('H:i d/m/Y', strtotime((string) $booking['departure_time']))) ?></dd>
                            <dt>Đến nơi dự kiến</dt>
                            <dd><?= e(date('H:i d/m/Y', strtotime((string) $booking['arrival_time']))) ?></dd>
                            <dt>Thanh toán</dt>
                            <dd><?= e($booking['payment_method'] ?? '-') ?> / <?= e($booking['payment_status'] ?? '-') ?></dd>
                        </dl>
                    </div>

                    <?php if ($isRoundTrip && !empty($booking['related_bookings'])): ?>
                        <div class="booking-panel mb-4">
                            <h2 class="h5 mb-3">Chuyến còn lại trong vé khứ hồi</h2>
                            <?php foreach ($booking['related_bookings'] as $related): ?>
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border rounded p-3 mb-2">
                                    <div>
                                        <span class="badge bg-light text-dark border mb-2">
                                            <?= e(($related['direction'] ?? 'outbound') === 'return' ? 'Chiều về' : 'Chiều đi') ?>
                                        </span>
                                        <div class="fw-semibold"><?= e($related['from_name']) ?> -> <?= e($related['to_name']) ?></div>
                                        <small class="text-muted">
                                            <?= e(date('H:i d/m/Y', strtotime((string) $related['departure_time']))) ?>
                                            · Ghế <?= e($related['seat_numbers'] ?? '-') ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="booking-status <?= e($related['status']) ?>"><?= e($related['status']) ?></span>
                                        <a class="btn btn-sm btn-outline-success d-block mt-2" href="<?= url('/booking/detail?id=' . (int) $related['id']) ?>">Xem vé</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="booking-panel">
                        <h2 class="h5 mb-3">Thông tin khách và ghế</h2>
                        <dl class="booking-info-list two-columns">
                            <dt>Khách hàng</dt>
                            <dd><?= e($booking['customer_name']) ?></dd>
                            <dt>Điện thoại</dt>
                            <dd><?= e($booking['customer_phone']) ?></dd>
                            <dt>Email</dt>
                            <dd><?= e($booking['customer_email'] ?? '-') ?></dd>
                            <dt>Tổng tiền</dt>
                            <dd><strong class="text-success"><?= number_format((float) $booking['total_amount'], 0, ',', '.') ?>đ</strong></dd>
                        </dl>

                        <div class="table-responsive mt-3">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Ghế</th>
                                        <th>Loại</th>
                                        <th class="text-end">Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach (($booking['seats'] ?? []) as $seat): ?>
                                    <tr>
                                        <td><strong><?= e($seat['seat_number']) ?></strong></td>
                                        <td><?= e($seat['seat_type']) ?></td>
                                        <td class="text-end"><?= number_format((float) $seat['price'], 0, ',', '.') ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="booking-panel text-center">
                        <h2 class="h5 mb-2">Vé điện tử</h2>
                        <p class="text-muted mb-3"><?= e($booking['ticket_code'] ?? '-') ?></p>
                        <?php if (!empty($booking['qr_code_path'])): ?>
                            <img class="ticket-qr-image mb-3" src="<?= asset($booking['qr_code_path']) ?>" alt="QR <?= e($booking['ticket_code'] ?? '') ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <span class="booking-status <?= e($booking['ticket_status'] ?? '') ?>"><?= e($booking['ticket_status'] ?? '-') ?></span>
                        </div>
                        <?php if (!empty($booking['ticket_code'])): ?>
                            <a class="btn btn-outline-success w-100" href="<?= url('/ticket/qr?ticket_code=' . rawurlencode((string) $booking['ticket_code'])) ?>">Xem QR</a>
                        <?php endif; ?>
                        <?php if (empty($booking['can_cancel']) && !empty($booking['cancel_reason'])): ?>
                            <p class="text-muted small mt-3 mb-0"><?= e($booking['cancel_reason']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
