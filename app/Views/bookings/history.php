<?php $pageJs = ['booking.js']; ?>
<?php $bookings = $bookings ?? []; ?>
<?php $isLoggedIn = \App\Core\Auth::check(); ?>
<section class="booking-page py-5">
    <div class="container">
        <?php if ($message = \App\Core\Session::getFlash('success')): ?>
            <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($message = \App\Core\Session::getFlash('error')): ?>
            <div class="alert alert-danger"><?= e($message) ?></div>
        <?php endif; ?>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <span class="section-kicker"><?= $isLoggedIn ? 'Tài khoản' : 'Tra cứu' ?></span>
                <h1 class="h3 mb-0"><?= $isLoggedIn ? 'Lịch sử đặt vé' : 'Tra cứu thông tin vé' ?></h1>
            </div>
            <a class="btn btn-success" href="<?= url('/trips/search') ?>">Đặt vé mới</a>
        </div>

        <!-- Tra cứu vé search widget -->
        <div class="booking-panel mb-4">
            <h2 class="h5 mb-3">Tra cứu nhanh bằng mã đặt vé</h2>
            <form action="<?= url('/booking/history') ?>" method="get" class="row g-3 align-items-center">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="booking_code" class="form-control form-control-lg text-uppercase" placeholder="Nhập mã booking (ví dụ: LB-20260524-XXXXXX)..." required value="<?= e($_GET['booking_code'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success btn-lg w-100">Tra cứu</button>
                </div>
            </form>
        </div>

        <div class="booking-panel booking-history-panel">
            <?php if (!$isLoggedIn): ?>
                <div class="text-center py-5">
                    <i class="bi bi-ticket-perforated display-5 text-muted"></i>
                    <h2 class="h5 mt-3">Tra cứu thông tin vé xe của bạn</h2>
                    <p class="text-muted mb-0">Vui lòng nhập mã đặt vé (được gửi qua email) vào ô tìm kiếm ở trên để xem chi tiết hành trình, vé xe và trạng thái thanh toán.</p>
                </div>
            <?php else: ?>
                <?php if ($bookings === []): ?>
                    <div class="text-center py-5 bg-white border border-light-subtle rounded-3">
                        <i class="bi bi-ticket-perforated display-5 text-muted"></i>
                        <h2 class="h5 mt-3">Chưa có booking nào</h2>
                        <p class="text-muted">Các vé đã đặt sẽ hiển thị tại đây.</p>
                    </div>
                <?php else: ?>
                    <!-- Desktop Table -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle booking-history-table">
                            <thead>
                                <tr>
                                    <th>Mã booking</th>
                                    <th>Tuyến xe</th>
                                    <th>Ngày đặt</th>
                                    <th>Khởi hành</th>
                                    <th>Ghế</th>
                                    <th class="text-end">Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <?php
                                $isRoundTrip = ($booking['trip_type'] ?? 'oneway') === 'roundtrip';
                                $directionLabel = ($booking['direction'] ?? 'outbound') === 'return' ? 'Chiều về' : 'Chiều đi';
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= e($booking['booking_code']) ?></strong>
                                        <?php if ($isRoundTrip): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle d-block mt-1">
                                                Khứ hồi · <?= e($directionLabel) ?>
                                            </span>
                                            <small class="text-muted d-block"><?= e($booking['booking_group_code'] ?? '') ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= e($booking['from_name']) ?> -> <?= e($booking['to_name']) ?></td>
                                    <td><?= e(date('d/m/Y H:i', strtotime((string) $booking['created_at']))) ?></td>
                                    <td><?= e(date('d/m/Y H:i', strtotime((string) $booking['departure_time']))) ?></td>
                                    <td>
                                        <span class="badge text-bg-light border"><?= (int) $booking['seat_count'] ?> ghế</span>
                                        <small class="text-muted d-block"><?= e($booking['seat_numbers'] ?? '') ?></small>
                                    </td>
                                    <td class="text-end"><?= number_format((float) $booking['total_amount'], 0, ',', '.') ?>đ</td>
                                    <td><span class="booking-status <?= e($booking['status']) ?>"><?= e($booking['status']) ?></span></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-success" href="<?= url('/booking/detail?id=' . (int) $booking['id']) ?>">Chi tiết</a>
                                        <?php if (!empty($booking['can_cancel'])): ?>
                                            <form class="d-inline js-cancel-booking-form" method="post" action="<?= url('/booking/cancel') ?>">
                                                <input type="hidden" name="booking_id" value="<?= e($booking['id']) ?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Hủy</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary" type="button" disabled title="<?= e($booking['cancel_reason'] ?? 'Không thể hủy vé này.') ?>">
                                                Không thể hủy
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card List -->
                    <div class="booking-mobile-cards d-md-none">
                        <?php foreach ($bookings as $booking): ?>
                            <?php
                            $isRoundTrip = ($booking['trip_type'] ?? 'oneway') === 'roundtrip';
                            $directionLabel = ($booking['direction'] ?? 'outbound') === 'return' ? 'Chiều về' : 'Chiều đi';
                            $statusClass = e($booking['status']);
                            ?>
                            <div class="booking-mobile-card">
                                <!-- Ticket Header -->
                                <div class="ticket-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="ticket-code-label">MÃ BOOKING</span>
                                        <h3 class="ticket-code mb-0"><?= e($booking['booking_code']) ?></h3>
                                        <?php if ($isRoundTrip): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle mt-1">
                                                Khứ hồi · <?= e($directionLabel) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="booking-status <?= $statusClass ?>"><?= $statusClass ?></span>
                                    </div>
                                </div>
                                
                                <!-- Ticket Divider with punch holes -->
                                <div class="ticket-divider">
                                    <span class="punch-hole left"></span>
                                    <span class="punch-line"></span>
                                    <span class="punch-hole right"></span>
                                </div>
                                
                                <!-- Ticket Body -->
                                <div class="ticket-body">
                                    <!-- Route -->
                                    <div class="ticket-route mb-3">
                                        <div class="route-station">
                                            <span class="station-label">Điểm đi</span>
                                            <strong class="station-name"><?= e($booking['from_name']) ?></strong>
                                        </div>
                                        <div class="route-arrow">
                                            <i class="bi bi-arrow-right text-success fs-4"></i>
                                        </div>
                                        <div class="route-station text-end">
                                            <span class="station-label">Điểm đến</span>
                                            <strong class="station-name"><?= e($booking['to_name']) ?></strong>
                                        </div>
                                    </div>
                                    
                                    <!-- Details grid -->
                                    <div class="row g-3 ticket-details mb-3">
                                        <div class="col-6">
                                            <span class="detail-label">Khởi hành</span>
                                            <div class="detail-value">
                                                <i class="bi bi-calendar3 text-muted me-1"></i>
                                                <?= e(date('d/m/Y H:i', strtotime((string) $booking['departure_time']))) ?>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <span class="detail-label">Ngày đặt</span>
                                            <div class="detail-value">
                                                <i class="bi bi-clock text-muted me-1"></i>
                                                <?= e(date('d/m/Y H:i', strtotime((string) $booking['created_at']))) ?>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <span class="detail-label">Chỗ ngồi</span>
                                            <div class="detail-value">
                                                <span class="badge text-bg-light border"><?= (int) $booking['seat_count'] ?> ghế</span>
                                                <small class="text-muted d-block mt-1"><?= e($booking['seat_numbers'] ?? '') ?></small>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <span class="detail-label">Tổng tiền</span>
                                            <div class="detail-value text-success fw-bold">
                                                <?= number_format((float) $booking['total_amount'], 0, ',', '.') ?>đ
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Ticket Actions -->
                                <div class="ticket-footer pt-3 border-top d-flex gap-2">
                                    <a class="btn btn-outline-success flex-grow-1 btn-sm" href="<?= url('/booking/detail?id=' . (int) $booking['id']) ?>">
                                        Chi tiết
                                    </a>
                                    <?php if (!empty($booking['can_cancel'])): ?>
                                        <form class="d-inline flex-grow-1 js-cancel-booking-form m-0" method="post" action="<?= url('/booking/cancel') ?>">
                                            <input type="hidden" name="booking_id" value="<?= e($booking['id']) ?>">
                                            <button class="btn btn-outline-danger w-100 btn-sm" type="submit">Hủy vé</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-outline-secondary flex-grow-1 btn-sm" type="button" disabled title="<?= e($booking['cancel_reason'] ?? 'Không thể hủy vé này.') ?>">
                                            Không thể hủy
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
