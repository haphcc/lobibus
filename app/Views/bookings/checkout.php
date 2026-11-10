<?php $pageJs = ['booking.js']; ?>
<?php
$trip = $trip ?? [];
$seats = $seats ?? [];
$user = $user ?? [];
$bookingMeta = $bookingMeta ?? ['trip_type' => 'oneway', 'direction' => 'outbound', 'booking_group_code' => null];
$totalAmount = (float) ($totalAmount ?? 0);
$isRoundTrip = ($bookingMeta['trip_type'] ?? 'oneway') === 'roundtrip';
$directionLabel = ($bookingMeta['direction'] ?? 'outbound') === 'return' ? 'Chiều về' : 'Chiều đi';
?>
<section class="booking-page py-5">
    <div class="container">
        <?php if ($message = \App\Core\Session::getFlash('error')): ?>
            <div class="alert alert-danger"><?= e($message) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="booking-panel">
                    <span class="section-kicker">Checkout</span>
                    <h1 class="h3 mb-4">Xác nhận thông tin đặt vé</h1>
                    <?php if ($isRoundTrip): ?>
                        <div class="alert alert-success py-2 mb-4">
                            Vé khứ hồi · <?= e($directionLabel) ?> · Mã nhóm <?= e($bookingMeta['booking_group_code'] ?? '') ?>
                        </div>
                    <?php endif; ?>

                    <div class="booking-route-box mb-4">
                        <div>
                            <small>Điểm đi</small>
                            <strong><?= e($trip['from_name'] ?? '-') ?></strong>
                        </div>
                        <i class="bi bi-arrow-right"></i>
                        <div>
                            <small>Điểm đến</small>
                            <strong><?= e($trip['to_name'] ?? '-') ?></strong>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="booking-mini-card">
                                <span>Xe</span>
                                <strong><?= e($trip['bus_name'] ?? '-') ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="booking-mini-card">
                                <span>Khởi hành</span>
                                <strong><?= !empty($trip['departure_time']) ? e(date('H:i d/m/Y', strtotime((string) $trip['departure_time']))) : '-' ?></strong>
                            </div>
                        </div>
                    </div>

                    <h2 class="h5 mb-3">Danh sách ghế</h2>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Ghế</th>
                                    <th>Loại ghế</th>
                                    <th class="text-end">Đơn giá</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($seats as $seat): ?>
                                <tr>
                                    <td><strong><?= e($seat['seat_number']) ?></strong></td>
                                    <td><?= e($seat['seat_type']) ?></td>
                                    <td class="text-end"><?= number_format((float) $seat['price'], 0, ',', '.') ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Tổng tiền</th>
                                    <th class="text-end text-success fs-5"><?= number_format($totalAmount, 0, ',', '.') ?>đ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="booking-panel">
                    <h2 class="h5 mb-3">Thông tin khách hàng</h2>
                    <form id="bookingConfirmForm" method="post" action="<?= url('/booking/store') ?>">
                        <input type="hidden" name="trip_id" value="<?= e($trip['id'] ?? '') ?>">
                        <input type="hidden" name="trip_type" value="<?= e($bookingMeta['trip_type'] ?? 'oneway') ?>">
                        <input type="hidden" name="direction" value="<?= e($bookingMeta['direction'] ?? 'outbound') ?>">
                        <input type="hidden" name="booking_group_code" value="<?= e($bookingMeta['booking_group_code'] ?? '') ?>">
                        <input type="hidden" name="next_trip_id" value="<?= e($bookingMeta['next_trip_id'] ?? '') ?>">
                        <input type="hidden" name="next_direction" value="<?= e($bookingMeta['next_direction'] ?? '') ?>">
                        <?php foreach ($seats as $seat): ?>
                            <input type="hidden" name="seat_ids[]" value="<?= e($seat['id']) ?>">
                        <?php endforeach; ?>

                        <div class="mb-3">
                            <label class="form-label" for="customerName">Họ tên</label>
                            <input id="customerName" name="customer_name" class="form-control" required value="<?= e($user['name'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="customerPhone">Số điện thoại</label>
                            <input id="customerPhone" name="customer_phone" class="form-control" required value="<?= e($user['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="customerEmail">Email</label>
                            <input id="customerEmail" name="customer_email" type="email" class="form-control" value="<?= e($user['email'] ?? '') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="paymentMethod">Phương thức thanh toán</label>
                            <select id="paymentMethod" name="payment_method" class="form-select">
                                <option value="cash">Tiền mặt tại quầy</option>
                                <option value="bank_transfer">Chuyển khoản</option>
                                <option value="momo">MoMo</option>
                                <option value="zalopay">ZaloPay</option>
                                <option value="card">Thẻ ngân hàng</option>
                            </select>
                        </div>
                        <button class="btn btn-success w-100" type="submit">Đặt vé</button>
                        <a class="btn btn-outline-secondary w-100 mt-2" href="<?= url('/booking/select-seat?trip_id=' . (int) ($trip['id'] ?? 0)) ?>">Chọn lại ghế</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
