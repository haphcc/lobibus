<footer class="site-footer mt-4">
    <div class="container">
        <div class="row align-items-start py-4 g-4">
            <div class="col-12 col-lg-5 footer-left">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="<?= asset('images/logo.svg') ?>" alt="Logo LobiBus" style="width:40px;height:40px;">
                    <strong style="font-size:1.8rem;color:#27ae60;">LobiBus</strong>
                </div>
                <div class="footer-contact mt-2">
                    <div>Địa chỉ: Số 12, Phố Chùa Bộc, Hà Nội</div>
                    <div>Hotline: 0936 363 363</div>
                    <div>Email: <a href="mailto:info@lobibus.vn">info@lobibus.vn</a></div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 footer-links">
                <strong>Liên kết nhanh</strong>
                <ul class="list-unstyled m-0">
                    <li><a href="<?= url('/trips/search') ?>">Đặt vé xe</a></li>
                    <li><a href="<?= url('/booking/history') ?>">Lịch sử đặt vé</a></li>
                    <li><a href="<?= url('/recommendations') ?>">Gợi ý chuyến</a></li>
                    <li><a href="<?= url('/chatbot') ?>">Hỗ trợ</a></li>
                </ul>
            </div>
            <div class="col-12 col-md-6 col-lg-4 footer-links">
                <strong>Hỗ trợ khách hàng</strong>
                <ul class="list-unstyled m-0">
                    <li><a href="<?= url('/login') ?>">Đăng nhập</a></li>
                    <li><a href="<?= url('/register') ?>">Đăng ký tài khoản</a></li>
                    <li><a href="<?= url('/forgot-password') ?>">Quên mật khẩu</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center py-3 footer-copy">© 2026 lobibus.com. Đã đăng ký bản quyền.</div>
        </div>
    </div>
</footer>
