<?php $pageJs = ['seat-selection.js?v=seatmap-layout-v2']; ?>
<?php
$trip = $trip ?? [];
$bookingMeta = $bookingMeta ?? ['trip_type' => 'oneway', 'direction' => 'outbound', 'booking_group_code' => null];
$departure = !empty($trip['departure_time']) ? date('H:i d/m/Y', strtotime((string) $trip['departure_time'])) : '-';
$arrival = !empty($trip['arrival_time']) ? date('H:i d/m/Y', strtotime((string) $trip['arrival_time'])) : '-';
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
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <span class="section-kicker">LobiBus</span>
                            <h1 class="h3 mb-1">Chọn ghế cho chuyến xe</h1>
                            <p class="text-muted mb-0"><?= e($trip['from_name'] ?? '-') ?> -> <?= e($trip['to_name'] ?? '-') ?></p>
                            <?php if ($isRoundTrip): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle mt-2">
                                    Vé khứ hồi · <?= e($directionLabel) ?> · <?= e($bookingMeta['booking_group_code'] ?? '') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <a class="btn btn-outline-secondary" href="<?= url('/trips/search') ?>">
                            <i class="bi bi-arrow-left"></i> Đổi chuyến
                        </a>
                    </div>

                    <div class="seat-legend mb-3">
                        <span><i class="seat-swatch available"></i> Ghế trống</span>
                        <span><i class="seat-swatch selected"></i> Đang chọn</span>
                        <span><i class="seat-swatch booked"></i> Đã đặt</span>
                    </div>

                    <div id="seatMap"
                         class="seat-map-grid"
                         data-trip-id="<?= e($trip['id'] ?? '') ?>"
                         data-checkout-url="<?= e(url('/booking/checkout')) ?>">
                        <div class="text-muted">Đang tải sơ đồ ghế...</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="booking-panel sticky-lg-top booking-summary-panel">
                    <h2 class="h5 mb-3">Thông tin chuyến</h2>
                    <dl class="booking-info-list">
                        <dt>Tuyến</dt>
                        <dd><?= e($trip['from_name'] ?? '-') ?> -> <?= e($trip['to_name'] ?? '-') ?></dd>
                        <dt>Xe</dt>
                        <dd><?= e($trip['bus_name'] ?? '-') ?></dd>
                        <dt>Khởi hành</dt>
                        <dd><?= e($departure) ?></dd>
                        <dt>Đến nơi dự kiến</dt>
                        <dd><?= e($arrival) ?></dd>
                        <dt>Giá/ghế</dt>
                        <dd><?= number_format((float) ($trip['price'] ?? 0), 0, ',', '.') ?>đ</dd>
                    </dl>

                    <hr>

                    <h3 class="h6">Ghế đang chọn</h3>
                    <div id="selectedSeatList" class="selected-seat-list text-muted mb-3">Chưa chọn ghế.</div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Tạm tính</span>
                        <strong id="seatTotalAmount">0đ</strong>
                    </div>

                    <form id="seatCheckoutForm" method="post" action="<?= url('/booking/checkout') ?>">
                        <input type="hidden" name="trip_id" value="<?= e($trip['id'] ?? '') ?>">
                        <input type="hidden" name="trip_type" value="<?= e($bookingMeta['trip_type'] ?? 'oneway') ?>">
                        <input type="hidden" name="direction" value="<?= e($bookingMeta['direction'] ?? 'outbound') ?>">
                        <input type="hidden" name="booking_group_code" value="<?= e($bookingMeta['booking_group_code'] ?? '') ?>">
                        <input type="hidden" name="next_trip_id" value="<?= e($bookingMeta['next_trip_id'] ?? '') ?>">
                        <input type="hidden" name="next_direction" value="<?= e($bookingMeta['next_direction'] ?? '') ?>">
                        <input type="hidden" name="seat_ids" id="selectedSeatIds" value="">
                        <button id="bookingSubmit" class="btn btn-success w-100" type="submit" disabled>
                            Xác nhận ghế
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
