<header class="site-header bg-white">
    <a href="<?= url('/') ?>">
        <div class="logo-wrap d-flex align-items-center gap-2">
            <img src="<?= asset('images/logo.svg') ?>" alt="LobiBus logo" class="logo">
            <span class="brand-name h5 mb-0">LobiBus</span>
        </div>
    </a>
    <div class="container d-flex align-items-center justify-content-between">
        <nav class="main-nav d-none d-md-flex" aria-label="Main navigation">
            <a class="nav-link px-3" href="<?= url('/trips/search') ?>">Dat chuyen</a>
            <a class="nav-link px-3" href="<?= url('/trips/search') ?>">Lich trinh</a>
            <a class="nav-link px-3" href="<?= url('/booking/history') ?>">Tra cuu ve</a>
            <a class="nav-link px-3" href="<?= url('/recommendations') ?>">Goi y chuyen</a>
            <a class="nav-link px-3" href="<?= url('/chatbot') ?>">Chatbot</a>
            <a class="nav-link px-3" href="<?= url('/login') ?>">Dang nhap</a>
        </nav>
    </div>
    <div class="actions d-flex align-items-center gap-2">
        <a class="btn btn-outline-primary btn-sm" href="<?= url('/admin') ?>">Admin</a>
    </div>
</header>
