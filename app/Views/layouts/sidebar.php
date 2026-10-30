<?php
$adminPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = base_path();
if ($basePath !== '' && str_starts_with($adminPath, $basePath)) {
    $adminPath = substr($adminPath, strlen($basePath)) ?: '/';
}
$adminPath = '/' . trim($adminPath, '/');

$items = [
    ['/admin', 'Dashboard'],
    ['/admin/users', 'Người dùng'],
    ['/admin/locations', 'Địa điểm'],
    ['/admin/routes', 'Tuyến xe'],
    ['/admin/buses', 'Xe'],
    ['/admin/seats', 'Ghế'],
    ['/admin/trips', 'Chuyến xe'],
    ['/admin/bookings', 'Đơn đặt vé'],
    ['/admin/payments', 'Thanh toán'],
    ['/admin/statistics', 'Thống kê'],
];
?>
<aside class="admin-sidebar">
    <a class="admin-brand" href="<?= url('/admin') ?>">
        <img src="<?= asset('images/logo.svg') ?>" alt="LobiBus logo">
        <span>Quản trị LobiBus</span>
    </a>
    <nav class="admin-nav">
        <?php foreach ($items as [$path, $label]): ?>
            <?php $active = $path === '/admin' ? $adminPath === '/admin' : str_starts_with($adminPath, $path); ?>
            <a class="<?= $active ? 'active' : '' ?>" href="<?= url($path) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
        <a href="<?= url('/') ?>">Xem trang chính</a>
        <a href="<?= url('/logout') ?>">Đăng xuất</a>
    </nav>
</aside>
