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
];
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-top">
        <a class="admin-brand" href="<?= url('/admin') ?>">
            <img src="<?= asset('images/logo.svg') ?>" alt="LobiBus logo">
            <span>Quản trị LobiBus</span>
        </a>
        <nav class="admin-nav">
            <?php foreach ($items as [$path, $label]): ?>
                <?php $active = $path === '/admin' ? $adminPath === '/admin' : str_starts_with($adminPath, $path); ?>
                <a class="<?= $active ? 'active' : '' ?>" href="<?= url($path) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
    
    <div class="admin-sidebar-footer">
        <hr class="sidebar-divider">
        <a class="admin-footer-link home-link" href="<?= url('/') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span>Xem trang chính</span>
        </a>
        <a class="admin-footer-link logout-link" href="<?= url('/logout') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>
