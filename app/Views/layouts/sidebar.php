<aside class="admin-sidebar">
    <a class="admin-brand" href="<?= url('/admin') ?>">
        <img src="<?= asset('images/logo.svg') ?>" alt="LobiBus logo">
        <span>LobiBus Admin</span>
    </a>
    <nav class="admin-nav">
        <a href="<?= url('/admin') ?>">Dashboard</a>
        <a href="<?= url('/admin/users') ?>">Người dùng</a>
        <a href="<?= url('/admin/locations') ?>">Địa điểm</a>
        <a href="<?= url('/admin/routes') ?>">Tuyến xe</a>
        <a href="<?= url('/admin/buses') ?>">Xe</a>
        <a href="<?= url('/admin/seats') ?>">Ghế</a>
        <a href="<?= url('/admin/trips') ?>">Chuyến xe</a>
        <a href="<?= url('/admin/bookings') ?>">Đơn đặt vé</a>
        <a href="<?= url('/admin/payments') ?>">Thanh toán</a>
        <a href="<?= url('/') ?>">Xem trang chính</a>
    </nav>
</aside>
