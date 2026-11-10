<?php
$currentUser = \App\Core\Auth::user();
$isAdmin = \App\Core\Auth::isAdmin();

// Determine the current URL path to set active states
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
$currentPath = parse_url($currentUri, PHP_URL_PATH) ?: '';

// Helper to check if a nav link is active
if (!function_exists('isNavActive')) {
    function isNavActive($path, $currentPath) {
        $basePath = base_path();
        $relativeCurrentPath = $currentPath;

        if ($basePath !== '' && str_starts_with($relativeCurrentPath, $basePath)) {
            $relativeCurrentPath = substr($relativeCurrentPath, strlen($basePath));
        }

        if ($relativeCurrentPath === '') {
            $relativeCurrentPath = '/';
        }

        $targetPath = $path === '' ? '/' : $path;
        if ($targetPath !== '/' && !str_starts_with($targetPath, '/')) {
            $targetPath = '/' . $targetPath;
        }

        if ($targetPath === '/') {
            return $relativeCurrentPath === '/' ? 'active' : '';
        }

        return ($relativeCurrentPath === $targetPath || str_starts_with($relativeCurrentPath, $targetPath . '/')) ? 'active' : '';
    }
}
?>
<header class="site-header">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <!-- Hamburger Button (Left) - Only visible on Mobile -->
        <button class="mobile-menu-btn d-md-none border-0 bg-transparent" aria-label="Menu" id="openDrawerBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-list text-white" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
            </svg>
        </button>

        <a href="<?= url('/') ?>" class="text-decoration-none">
            <div class="logo-wrap d-flex align-items-center gap-2">
                <img src="<?= asset('images/logo.svg') ?>" alt="Logo LobiBus" class="logo">
                <span class="brand-name h5 mb-0">LobiBus</span>
            </div>
        </a>

        <!-- Desktop Navigation Links (Hidden on Mobile) -->
        <nav class="main-nav d-none d-md-flex align-items-center" aria-label="Điều hướng chính">
            <a class="nav-link px-3 <?= isNavActive('/trips/search', $currentPath) ?>" href="<?= url('/') ?>">Đặt chuyến</a>
            <a class="nav-link px-3 <?= isNavActive('/trips/schedule', $currentPath) ?>" href="<?= url('/trips/schedule') ?>">Lịch trình</a>
            <a class="nav-link px-3 <?= isNavActive('/booking/history', $currentPath) ?>" href="<?= url('/booking/history') ?>">Tra cứu vé</a>
            <a class="nav-link px-3 <?= isNavActive('/recommendations', $currentPath) ?>" href="<?= url('/recommendations') ?>">Gợi ý chuyến</a>
            <a class="nav-link px-3 <?= isNavActive('/news', $currentPath) ?>" href="<?= url('/news') ?>">Tin tức</a>
        </nav>

        <div class="actions d-flex align-items-center gap-2">
            <?php if ($currentUser !== null): ?>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle navbar-user-dropdown" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-initials d-flex align-items-center justify-content-center rounded-circle bg-white text-success fw-bold">
                            <?= strtoupper(substr(e($currentUser['name'] ?: $currentUser['email']), 0, 1)) ?>
                        </div>
                        <span class="user-name-text d-none d-lg-inline fw-semibold text-white">
                            <?= e($currentUser['name'] ?: $currentUser['email']) ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu">
                        <li class="dropdown-header">
                            <span class="d-block text-muted small">Tài khoản</span>
                            <strong class="text-dark"><?= e($currentUser['name'] ?: $currentUser['email']) ?></strong>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?= url('/account') ?>">
                                <span>👤</span> Tài khoản
                            </a>
                        </li>
                        <?php if ($isAdmin): ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="<?= url('/admin') ?>">
                                    <span>⚙️</span> Quản trị
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li>
                            <form method="post" action="<?= url('/logout') ?>">
                            <button class="dropdown-item text-danger d-flex align-items-center gap-2 fw-semibold" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right me-1" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                </svg>
                                Đăng xuất
                            </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <a class="btn btn-login btn-sm" href="<?= url('/login') ?>">Đăng nhập</a>
                <a class="btn btn-register btn-sm d-none d-sm-inline-block" href="<?= url('/register') ?>">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Mobile Menu Drawer (Left slide-in) -->
<div class="mobile-menu-drawer d-md-none" id="mobileDrawer">
    <div class="drawer-header d-flex align-items-center justify-content-between p-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <img src="<?= asset('images/logo.svg') ?>" alt="Logo LobiBus" style="width:30px;height:30px;">
            <strong class="text-success h5 mb-0">LobiBus</strong>
        </div>
        <button class="drawer-close-btn border-0 bg-transparent text-muted" id="closeDrawerBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
            </svg>
        </button>
    </div>
    <div class="drawer-body p-3">
        <nav class="d-flex flex-column gap-2">
            <a class="drawer-link p-3 rounded text-decoration-none <?= isNavActive('/trips/search', $currentPath) ?>" href="<?= url('/') ?>">Đặt chuyến</a>
            <a class="drawer-link p-3 rounded text-decoration-none <?= isNavActive('/trips/schedule', $currentPath) ?>" href="<?= url('/trips/schedule') ?>">Lịch trình</a>
            <a class="drawer-link p-3 rounded text-decoration-none <?= isNavActive('/booking/history', $currentPath) ?>" href="<?= url('/booking/history') ?>">Tra cứu vé</a>
            <a class="drawer-link p-3 rounded text-decoration-none <?= isNavActive('/recommendations', $currentPath) ?>" href="<?= url('/recommendations') ?>">Gợi ý chuyến</a>
            <a class="drawer-link p-3 rounded text-decoration-none <?= isNavActive('/news', $currentPath) ?>" href="<?= url('/news') ?>">Tin tức</a>
        </nav>
    </div>
</div>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay d-md-none" id="drawerOverlay"></div>

<!-- JavaScript to toggle drawer -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const openBtn = document.getElementById('openDrawerBtn');
    const closeBtn = document.getElementById('closeDrawerBtn');
    const drawer = document.getElementById('mobileDrawer');
    const overlay = document.getElementById('drawerOverlay');

    function openDrawer() {
        drawer.classList.add('active');
        overlay.classList.add('active');
        document.body.classList.add('overflow-hidden');
    }

    function closeDrawer() {
        drawer.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('overflow-hidden');
    }

    if (openBtn) openBtn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);
});
</script>
