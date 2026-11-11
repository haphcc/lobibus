<?php $pageJs = ['booking.js']; ?>
<?php
$trip = $trip ?? [];
$seats = $seats ?? [];
$user = $user ?? [];
$bookingMeta = $bookingMeta ?? ['trip_type' => 'oneway', 'direction' => 'outbound', 'booking_group_code' => null, 'seats' => 1];
$roundTripLegs = isset($roundTripLegs) && is_array($roundTripLegs) ? $roundTripLegs : [];
$totalAmount = (float) ($totalAmount ?? 0);
$isRoundTrip = ($bookingMeta['trip_type'] ?? 'oneway') === 'roundtrip';
$directionLabel = ($bookingMeta['direction'] ?? 'outbound') === 'return' ? 'Chiều về' : 'Chiều đi';
$returnUrl = (string) ($bookingMeta['return_url'] ?? url('/trips/search'));
$backTrip = $roundTripLegs[0]['trip'] ?? $trip;
$backSeatQuery = [
    'trip_id' => (int) ($backTrip['id'] ?? 0),
    'seats' => max(1, min(5, (int) ($bookingMeta['seats'] ?? count($seats) ?: 1))),
];
if ($isRoundTrip) {
    $backSeatQuery['trip_type'] = 'roundtrip';
    $backSeatQuery['direction'] = (string) ($roundTripLegs[0]['direction'] ?? $bookingMeta['direction'] ?? 'outbound');
    $backSeatQuery['booking_group_code'] = (string) ($bookingMeta['booking_group_code'] ?? '');
    if (isset($roundTripLegs[1]['trip']['id'])) {
        $backSeatQuery['next_trip_id'] = (int) $roundTripLegs[1]['trip']['id'];
        $backSeatQuery['next_direction'] = (string) ($roundTripLegs[1]['direction'] ?? 'return');
    }
}
$backSeatUrl = url('/booking/select-seat?' . http_build_query($backSeatQuery));
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
                            Vé khứ hồi · <?= e($roundTripLegs !== [] ? 'Đủ 2 chiều' : $directionLabel) ?> · Mã nhóm <?= e($bookingMeta['booking_group_code'] ?? '') ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($roundTripLegs !== []): ?>
                        <?php foreach ($roundTripLegs as $leg): ?>
                            <?php
                            $legTrip = $leg['trip'] ?? [];
                            $legSeats = $leg['seats'] ?? [];
                            $legLabel = ($leg['direction'] ?? 'outbound') === 'return' ? 'Chiều về' : 'Chiều đi';
                            ?>
                            <h2 class="h5 mb-3"><?= e($legLabel) ?></h2>
                            <div class="booking-route-box mb-4">
                                <div>
                                    <small>Điểm đi</small>
                                    <strong><?= e($legTrip['from_name'] ?? '-') ?></strong>
                                </div>
                                <i class="bi bi-arrow-right"></i>
                                <div>
                                    <small>Điểm đến</small>
                                    <strong><?= e($legTrip['to_name'] ?? '-') ?></strong>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="booking-mini-card">
                                        <span>Xe</span>
                                        <strong><?= e($legTrip['bus_name'] ?? '-') ?></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="booking-mini-card">
                                        <span>Khởi hành</span>
                                        <strong><?= !empty($legTrip['departure_time']) ? e(date('H:i d/m/Y', strtotime((string) $legTrip['departure_time']))) : '-' ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Ghế</th>
                                            <th>Loại ghế</th>
                                            <th class="text-end">Đơn giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($legSeats as $seat): ?>
                                        <tr>
                                            <td><strong><?= e($seat['seat_number']) ?></strong></td>
                                            <td><?= e($seat['seat_type']) ?></td>
                                            <td class="text-end"><?= number_format((float) $seat['price'], 0, ',', '.') ?>đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
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
                            </table>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                        <strong>Tổng tiền</strong>
                        <strong class="text-success fs-5"><?= number_format($totalAmount, 0, ',', '.') ?>đ</strong>
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
                        <input type="hidden" name="seats" value="<?= e((string) ($bookingMeta['seats'] ?? count($seats))) ?>">
                        <input type="hidden" name="return_url" value="<?= e($returnUrl) ?>">

                        <?php if ($roundTripLegs !== []): ?>
                            <?php foreach ($roundTripLegs as $legIndex => $leg): ?>
                                <input type="hidden" name="legs[<?= (int) $legIndex ?>][trip_id]" value="<?= e($leg['trip']['id'] ?? '') ?>">
                                <input type="hidden" name="legs[<?= (int) $legIndex ?>][direction]" value="<?= e($leg['direction'] ?? 'outbound') ?>">
                                <?php foreach (($leg['seats'] ?? []) as $seat): ?>
                                    <input type="hidden" name="legs[<?= (int) $legIndex ?>][seat_ids][]" value="<?= e($seat['id']) ?>">
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php foreach ($seats as $seat): ?>
                                <input type="hidden" name="seat_ids[]" value="<?= e($seat['id']) ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>

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
                            <input id="customerEmail" name="customer_email" type="email" class="form-control" required value="<?= e($user['email'] ?? '') ?>">
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
                        <a class="btn btn-outline-secondary w-100 mt-2" href="<?= e($backSeatUrl) ?>">Đổi chuyến</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
