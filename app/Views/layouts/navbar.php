<?php
$currentUser = \App\Core\Auth::user();
$isAdmin = \App\Core\Auth::isAdmin();
?>
<header class="site-header bg-white">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <a href="<?= url('/') ?>" class="text-decoration-none">
            <div class="logo-wrap d-flex align-items-center gap-2">
                <img src="<?= asset('images/logo.svg') ?>" alt="Logo LobiBus" class="logo">
                <span class="brand-name h5 mb-0">LobiBus</span>
            </div>
        </a>

        <nav class="main-nav d-none d-md-flex align-items-center" aria-label="Điều hướng chính">
            <a class="nav-link px-3" href="<?= url('/trips/search') ?>">Đặt chuyến</a>
            <a class="nav-link px-3" href="<?= url('/trips/search') ?>">Lịch trình</a>
            <a class="nav-link px-3" href="<?= url('/booking/history') ?>">Tra cứu vé</a>
            <a class="nav-link px-3" href="<?= url('/recommendations') ?>">Gợi ý chuyến</a>
            <a class="nav-link px-3" href="<?= url('/chatbot') ?>">Chat bot</a>
        </nav>

        <div class="actions d-flex align-items-center gap-2">
            <?php if ($currentUser !== null): ?>
                <span class="d-none d-lg-inline text-muted small"><?= e($currentUser['name'] ?: $currentUser['email']) ?></span>
                <?php if ($isAdmin): ?>
                    <a class="btn btn-outline-primary btn-sm" href="<?= url('/admin') ?>">Admin</a>
                <?php endif; ?>
                <a class="btn btn-outline-secondary btn-sm" href="<?= url('/logout') ?>">Đăng xuất</a>
            <?php else: ?>
                <a class="btn btn-outline-secondary btn-sm" href="<?= url('/login') ?>">Đăng nhập</a>
                <a class="btn btn-success btn-sm" href="<?= url('/register') ?>">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</header>
