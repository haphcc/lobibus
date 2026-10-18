<footer class="site-footer mt-4">
    <div class="container">
        <div class="row align-items-start py-4">
            <div class="col-12 col-md-6 footer-left">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="<?= asset('images/logo.svg') ?>" alt="LobiBus logo" style="width:40px;height:40px;">
                    <strong style="font-size:1.8rem;color:#27ae60;">LobiBus</strong>
                </div>
                <div class="footer-contact mt-2">
                    <div>Dia chi: So 12, Pho Chua Boc, Ha Noi</div>
                    <div>Hotline: 0936 363 363</div>
                    <div>Email: <a href="mailto:info@lobibus.vn">info@lobibus.vn</a></div>
                </div>
            </div>
            <div class="col-12 col-md-6 footer-links">
                <a href="<?= url('/trips/search') ?>">Dat ve xe</a>
                <a href="<?= url('/booking/history') ?>" class="ms-3">Lich su dat ve</a>
                <a href="<?= url('/chatbot') ?>" class="ms-3">Ho tro</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center py-3 footer-copy">© 2026 lobibus.com. All rights reserved.</div>
        </div>
    </div>
</footer>
