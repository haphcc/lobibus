<?php
$summary = $summary ?? ['users' => 0, 'trips' => 0, 'bookings' => 0, 'tickets' => 0, 'revenue' => 0];
$revenueByDay = $revenueByDay ?? [];
$bookingStatusBreakdown = $bookingStatusBreakdown ?? [];
$paymentMethodBreakdown = $paymentMethodBreakdown ?? [];
$tripStatusBreakdown = $tripStatusBreakdown ?? [];
$usersByRole = $usersByRole ?? [];
$topRoutes = $topRoutes ?? [];
$upcomingTrips = $upcomingTrips ?? [];
$recentBookings = $recentBookings ?? [];
?>
<section class="statistics-page">
    <div class="statistics-header">
        <div>
            <span class="statistics-kicker">Báo cáo vận hành</span>
            <h1>Thống kê LobiBus</h1>
            <p>Theo dõi doanh thu, vé, chuyến xe và người dùng từ dữ liệu hệ thống.</p>
        </div>
        <a class="btn btn-outline-secondary" href="<?= url('/admin') ?>">Dashboard</a>
    </div>

    <div class="statistics-metrics">
        <div class="statistics-card primary">
            <span>Tổng doanh thu</span>
            <strong><?= number_format((float) $summary['revenue'], 0, ',', '.') ?>đ</strong>
            <small>Chỉ tính thanh toán đã hoàn tất</small>
        </div>
        <div class="statistics-card">
            <span>Tổng vé</span>
            <strong><?= number_format((int) $summary['tickets']) ?></strong>
            <small>Vé đã phát sinh trong hệ thống</small>
        </div>
        <div class="statistics-card">
            <span>Chuyến xe</span>
            <strong><?= number_format((int) $summary['trips']) ?></strong>
            <small>Bao gồm mọi trạng thái chuyến</small>
        </div>
        <div class="statistics-card">
            <span>Người dùng</span>
            <strong><?= number_format((int) $summary['users']) ?></strong>
            <small>Tài khoản đã seed hoặc đăng ký</small>
        </div>
    </div>

    <div class="statistics-grid">
        <section class="statistics-panel">
            <div class="statistics-panel-heading">
                <h2>Doanh thu theo ngày</h2>
                <span>Theo ngày khởi hành</span>
            </div>
            <canvas id="revenueChart" height="118"></canvas>
        </section>

        <section class="statistics-panel">
            <div class="statistics-panel-heading">
                <h2>Trạng thái đặt vé</h2>
                <span>Tỷ lệ booking</span>
            </div>
            <canvas id="bookingStatusChart" height="210"></canvas>
        </section>
    </div>

    <div class="statistics-grid-secondary">
        <section class="statistics-panel">
            <div class="statistics-panel-heading">
                <h2>Phương thức thanh toán</h2>
                <span>Theo số giao dịch</span>
            </div>
            <canvas id="paymentMethodChart" height="180"></canvas>
        </section>

        <section class="statistics-panel">
            <div class="statistics-panel-heading">
                <h2>Trạng thái chuyến xe</h2>
                <span>Toàn bộ lịch chạy</span>
            </div>
            <canvas id="tripStatusChart" height="180"></canvas>
        </section>

        <section class="statistics-panel">
            <div class="statistics-panel-heading">
                <h2>Người dùng theo vai trò</h2>
                <span>Cơ cấu tài khoản</span>
            </div>
            <canvas id="userRoleChart" height="180"></canvas>
        </section>
    </div>

    <div class="statistics-grid">
        <section class="statistics-panel">
            <div class="statistics-panel-heading">
                <h2>Tuyến nổi bật</h2>
                <span>Xếp theo lượt đặt</span>
            </div>
            <div class="table-responsive">
                <table class="statistics-table">
                    <thead>
                        <tr>
                            <th>Tuyến</th>
                            <th>Lượt đặt</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($topRoutes as $route): ?>
                        <tr>
                            <td><?= e($route['route']) ?></td>
                            <td><?= number_format((int) $route['bookings']) ?></td>
                            <td><?= number_format((float) $route['revenue'], 0, ',', '.') ?>đ</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="statistics-panel">
            <div class="statistics-panel-heading">
                <h2>Chuyến sắp chạy</h2>
                <span>Lịch gần nhất</span>
            </div>
            <div class="statistics-list">
            <?php foreach ($upcomingTrips as $trip): ?>
                <article class="statistics-list-item">
                    <div>
                        <strong><?= e($trip['route']) ?></strong>
                        <span><?= e($trip['bus_name']) ?> · <?= e($trip['departure_time']) ?></span>
                    </div>
                    <span class="statistics-pill"><?= number_format((int) $trip['available_seats']) ?> ghế trống</span>
                </article>
            <?php endforeach; ?>
            </div>
        </section>
    </div>

    <section class="statistics-panel">
        <div class="statistics-panel-heading">
            <h2>Đặt vé gần đây</h2>
            <span>Dữ liệu mới nhất</span>
        </div>
        <div class="table-responsive">
            <table class="statistics-table">
                <thead>
                    <tr>
                        <th>Mã đặt vé</th>
                        <th>Khách hàng</th>
                        <th>Tuyến</th>
                        <th>Giá trị</th>
                        <th>Booking</th>
                        <th>Thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recentBookings as $booking): ?>
                    <tr>
                        <td><?= e($booking['booking_code']) ?></td>
                        <td>
                            <strong><?= e($booking['customer_name']) ?></strong>
                            <span><?= e($booking['customer_phone']) ?></span>
                        </td>
                        <td><?= e($booking['route']) ?></td>
                        <td><?= number_format((float) $booking['total_amount'], 0, ',', '.') ?>đ</td>
                        <td><span class="statistics-pill"><?= e($booking['status']) ?></span></td>
                        <td><span class="statistics-pill muted"><?= e($booking['payment_status'] ?? 'chưa có') ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>

<script>
window.LobiBusStatisticData = {
  revenueByDay: <?= json_encode($revenueByDay, JSON_UNESCAPED_UNICODE) ?>,
  bookingStatusBreakdown: <?= json_encode($bookingStatusBreakdown, JSON_UNESCAPED_UNICODE) ?>,
  paymentMethodBreakdown: <?= json_encode($paymentMethodBreakdown, JSON_UNESCAPED_UNICODE) ?>,
  tripStatusBreakdown: <?= json_encode($tripStatusBreakdown, JSON_UNESCAPED_UNICODE) ?>,
  usersByRole: <?= json_encode($usersByRole, JSON_UNESCAPED_UNICODE) ?>,
};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
