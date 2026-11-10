<?php
$trip = $trip ?? null;
$departure = $trip && !empty($trip['departure_time']) ? date('H:i d/m/Y', strtotime((string) $trip['departure_time'])) : '-';
$arrival = $trip && !empty($trip['arrival_time']) ? date('H:i d/m/Y', strtotime((string) $trip['arrival_time'])) : '-';
?>
<section class="booking-page py-5">
    <div class="container">
        <?php if (!$trip): ?>
            <div class="booking-panel">
                <h1 class="h4">Không tìm thấy chuyến xe</h1>
                <p class="text-muted">Chuyến xe không tồn tại hoặc chưa có mã chuyến hợp lệ.</p>
                <a class="btn btn-success" href="<?= url('/trips/search') ?>">Tìm chuyến khác</a>
            </div>
        <?php else: ?>
            <div class="booking-panel">
                <span class="section-kicker">Chi tiết chuyến</span>
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <h1 class="h3 mb-2"><?= e($trip['from_name']) ?> -> <?= e($trip['to_name']) ?></h1>
                        <p class="text-muted mb-0"><?= e($trip['bus_name']) ?> · <?= e($trip['bus_type'] ?? '-') ?></p>
                    </div>
                    <?php if (($trip['status'] ?? '') === 'scheduled'): ?>
                        <a class="btn btn-success" href="<?= url('/booking/select-seat?trip_id=' . (int) $trip['id']) ?>">
                            Chọn ghế
                        </a>
                    <?php endif; ?>
                </div>

                <div class="booking-route-box mb-4">
                    <div>
                        <small>Điểm đi</small>
                        <strong><?= e($trip['from_name']) ?></strong>
                        <?php if (!empty($trip['from_address'])): ?>
                            <span class="text-muted d-block small"><?= e($trip['from_address']) ?></span>
                        <?php endif; ?>
                    </div>
                    <i class="bi bi-arrow-right"></i>
                    <div>
                        <small>Điểm đến</small>
                        <strong><?= e($trip['to_name']) ?></strong>
                        <?php if (!empty($trip['to_address'])): ?>
                            <span class="text-muted d-block small"><?= e($trip['to_address']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <dl class="booking-info-list two-columns">
                    <dt>Khởi hành</dt>
                    <dd><?= e($departure) ?></dd>
                    <dt>Đến nơi dự kiến</dt>
                    <dd><?= e($arrival) ?></dd>
                    <dt>Giá vé</dt>
                    <dd><strong class="text-success"><?= number_format((float) $trip['price'], 0, ',', '.') ?>đ</strong></dd>
                    <dt>Trạng thái</dt>
                    <dd><?= e($trip['status']) ?></dd>
                    <dt>Quãng đường</dt>
                    <dd><?= !empty($trip['distance_km']) ? e($trip['distance_km']) . ' km' : '-' ?></dd>
                    <dt>Thời gian dự kiến</dt>
                    <dd><?= !empty($trip['duration_minutes']) ? e((string) $trip['duration_minutes']) . ' phút' : '-' ?></dd>
                </dl>
            </div>
        <?php endif; ?>
    </div>
</section>
