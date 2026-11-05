<footer class="site-footer">
    <div class="container">
        <div class="row align-items-start g-5">
            <!-- Brand & Contact -->
            <div class="col-12 col-lg-4 footer-brand">
                <a href="<?= url('/') ?>" class="d-flex align-items-center gap-2 mb-4 text-decoration-none">
                    <img src="<?= asset('images/logo.svg') ?>" alt="Logo LobiBus" class="footer-logo">
                    <span class="footer-brand-name">LobiBus</span>
                </a>
                <p class="footer-desc mb-4">
                    Nền tảng đặt vé xe khách trực tuyến thông minh, mang đến cho bạn những chuyến đi an toàn, tiện lợi và tiết kiệm nhất.
                </p>
                <div class="footer-contact">
                    <div class="contact-item">
                        <span>📍 Số 12, Phố Chùa Bộc, Đống Đa, Hà Nội</span>
                    </div>
                    <div class="contact-item">
                        <span>📞 0936 363 363</span>
                    </div>
                    <div class="contact-item">
                        <span>✉️ <a href="mailto:info@lobibus.vn">info@lobibus.vn</a></span>
                    </div>
                </div>
            </div>

            <!-- Links 1 -->
            <div class="col-12 col-sm-6 col-lg-3 offset-lg-1 footer-links">
                <h5 class="footer-title">Khám phá</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= url('/trips/search') ?>">Đặt vé xe</a></li>
                    <li><a href="<?= url('/trips/schedule') ?>">Lịch trình</a></li>
                    <li><a href="<?= url('/recommendations') ?>">Gợi ý chuyến</a></li>
                    <li><a href="<?= url('/news') ?>">Tin tức LobiBus</a></li>
                </ul>
            </div>

            <!-- Links 2 -->
            <div class="col-12 col-sm-6 col-lg-3 footer-links">
                <h5 class="footer-title">Khách hàng</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= url('/booking/history') ?>">Tra cứu vé</a></li>
                    <li><a href="<?= url('/login') ?>">Đăng nhập</a></li>
                    <li><a href="<?= url('/register') ?>">Đăng ký tài khoản</a></li>
                    <li><a href="<?= url('/forgot-password') ?>">Quên mật khẩu</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="footer-copy">
                &copy; <?= date('Y') ?> LobiBus. Tất cả quyền được bảo lưu.
            </div>
            <div class="footer-social mt-3 mt-md-0 d-flex gap-3">
                <a href="#" class="social-link" aria-label="Facebook">FB</a>
                <a href="#" class="social-link" aria-label="YouTube">YT</a>
                <a href="#" class="social-link" aria-label="Instagram">IG</a>
            </div>
        </div>
    </div>
</footer>
