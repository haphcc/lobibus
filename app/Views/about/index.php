<style>
/* Style riêng cao cấp cho trang Về Chúng Tôi */
.about-hero {
    background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    color: white;
    padding: 5rem 1rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border-radius: 0 0 40px 40px;
    box-shadow: 0 10px 30px rgba(15, 118, 110, 0.15);
}

.about-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
    pointer-events: none;
    transform: rotate(15deg);
}

.about-hero h1 {
    font-size: 3rem;
    font-weight: 850;
    margin-bottom: 1.2rem;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.about-hero p {
    font-size: 1.25rem;
    opacity: 0.9;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}

.about-content-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #e2ece7;
    box-shadow: 0 10px 30px rgba(15, 118, 110, 0.04);
    padding: 2.5rem;
    height: 100%;
    transition: all 0.3s ease;
}

.about-content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(15, 118, 110, 0.08);
    border-color: #0f766e;
}

.feature-icon-box {
    width: 60px;
    height: 60px;
    background: #e6f4f2;
    color: #0f766e;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.about-content-card:hover .feature-icon-box {
    background: #0f766e;
    color: white;
    transform: scale(1.1) rotate(5deg);
}

.contact-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.contact-info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.contact-info-icon {
    width: 42px;
    height: 42px;
    background: #e6f4f2;
    color: #0f766e;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.contact-info-text strong {
    display: block;
    color: #0f172a;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}

.contact-info-text span {
    color: #475569;
    font-size: 1.05rem;
}

/* Glassmorphism Feedback Card */
.feedback-glass-card {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(15, 118, 110, 0.15);
    border-radius: 24px;
    padding: 2.5rem;
    box-shadow: 0 15px 35px rgba(15, 118, 110, 0.06);
}

.feedback-form .form-control, .feedback-form .form-select {
    border: 2px solid #cbd5e1;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: white;
}

.feedback-form .form-control:focus, .feedback-form .form-select:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.15);
    background: white;
}

.feedback-form label {
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
}

.btn-send-feedback {
    background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    color: white;
    border: none;
    font-weight: 700;
    padding: 0.8rem 2rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2);
}

.btn-send-feedback:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 118, 110, 0.3);
    color: white;
    opacity: 0.95;
}

.btn-send-feedback:active {
    transform: translateY(0);
}

/* Map Container styling */
.map-wrapper {
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid #e2ece7;
    box-shadow: 0 10px 30px rgba(15, 118, 110, 0.04);
}

@media (max-width: 768px) {
    .about-hero {
        padding: 4rem 1rem;
        border-radius: 0 0 20px 20px;
    }
    
    .about-hero h1 {
        font-size: 2.25rem;
    }
    
    .about-content-card, .feedback-glass-card {
        padding: 1.75rem;
    }
}
</style>

<!-- Hero Section -->
<section class="about-hero mb-5">
    <div class="container">
        <h1>Về chúng tôi</h1>
        <p>LobiBus - Hành trình trọn vẹn, kết nối mọi miền Tổ quốc. Chúng tôi tự hào mang lại giải pháp đặt vé xe trực tuyến an toàn, sang trọng và hiện đại.</p>
    </div>
</section>

<div class="container mb-5" style="max-width: 1200px;">
    <!-- Giới thiệu ngắn về LobiBus -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="about-content-card">
                <div class="feature-icon-box">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h4 class="fw-bold text-dark mb-3">An toàn & Đáng tin</h4>
                <p class="text-muted mb-0">Hệ thống đội xe đời mới chất lượng cao, tài xế được đào tạo bài bản chuyên nghiệp cùng chính sách bảo hiểm hành khách toàn diện.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="about-content-card">
                <div class="feature-icon-box">
                    <i class="bi bi-gem"></i>
                </div>
                <h4 class="fw-bold text-dark mb-3">Dịch vụ 5 Sao</h4>
                <p class="text-muted mb-0">Trang bị ghế massage cao cấp, wifi tốc độ cao, nước uống miễn phí và cổng sạc USB tại mỗi ghế đảm bảo chuyến đi thoải mái nhất.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="about-content-card">
                <div class="feature-icon-box">
                    <i class="bi bi-phone"></i>
                </div>
                <h4 class="fw-bold text-dark mb-3">Đặt vé nhanh chóng</h4>
                <p class="text-muted mb-0">Nền tảng công nghệ đặt vé tiên tiến nhất, hỗ trợ thanh toán đa kênh chỉ trong vài lần chạm tay và tra cứu vé tức thời.</p>
            </div>
        </div>
    </div>

    <!-- Hàng Thông tin liên hệ & Góp ý -->
    <div class="row g-4 mb-5">
        <!-- Thông tin liên hệ -->
        <div class="col-lg-5">
            <div class="about-content-card d-flex flex-column justify-content-between">
                <div>
                    <h3 class="fw-bold text-dark mb-4">Thông tin liên hệ</h3>
                    <p class="text-muted mb-4">Mọi thắc mắc, yêu cầu hỗ trợ hoặc đặt vé trực tiếp vui lòng liên hệ với LobiBus thông qua các kênh liên lạc sau đây:</p>
                    
                    <ul class="contact-info-list mb-4">
                        <li class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="contact-info-text">
                                <strong>Địa chỉ văn phòng</strong>
                                <span>Số 12, Phố Chùa Bộc, Đống Đa, Hà Nội</span>
                            </div>
                        </li>
                        <li class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div class="contact-info-text">
                                <strong>Hotline hỗ trợ</strong>
                                <span>1900 6868 (Hỗ trợ 24/7)</span>
                            </div>
                        </li>
                        <li class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="contact-info-text">
                                <strong>Email liên hệ</strong>
                                <span>support@lobibus.vn</span>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="p-3 bg-light rounded-4 d-flex align-items-center gap-3">
                    <i class="bi bi-clock-fill text-success fs-3"></i>
                    <div>
                        <small class="text-muted d-block">Giờ làm việc</small>
                        <strong class="text-dark">Cả ngày (00:00 - 24:00)</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu mẫu Góp ý & Đánh giá -->
        <div class="col-lg-7">
            <div class="feedback-glass-card">
                <h3 class="fw-bold text-dark mb-2">Đánh giá & Góp ý</h3>
                <p class="text-muted mb-4">Ý kiến đóng góp của bạn rất quan trọng để giúp chúng tôi ngày một nâng cao dịch vụ xe khách tốt hơn.</p>

                <!-- Thông báo -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= e($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form class="feedback-form" action="<?= url('/about/feedback') ?>" method="post">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="feedbackName" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="feedbackName" name="name" 
                                   placeholder="Nguyễn Văn A" value="<?= e($old['name'] ?? ($currentUser['name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="feedbackPhone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="feedbackPhone" name="phone" 
                                   placeholder="0912345678" value="<?= e($old['phone'] ?? ($currentUser['phone'] ?? '')) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="feedbackEmail" class="form-label">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="feedbackEmail" name="email" 
                               placeholder="name@example.com" value="<?= e($old['email'] ?? ($currentUser['email'] ?? '')) ?>" required>
                    </div>

                    <!-- Mức đánh giá sao (1 - 5) -->
                    <div class="mb-3">
                        <label class="form-label d-block mb-1">Đánh giá độ hài lòng <span class="text-danger">*</span></label>
                        <div class="star-rating d-inline-flex gap-2 fs-2 text-muted" style="cursor: pointer; line-height: 1;" id="starRatingContainer">
                            <?php $currentRating = (int) ($old['rating'] ?? 0); ?>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php 
                                    $starClass = $i <= $currentRating ? 'bi-star-fill text-warning' : 'bi-star';
                                ?>
                                <i class="bi <?= $starClass ?> star-item" data-value="<?= $i ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="ratingInput" name="rating" value="<?= $currentRating > 0 ? $currentRating : '' ?>" required>
                    </div>

                    <!-- Tùy chọn Chuyến xe đánh giá (Chỉ hiển thị khi đã đăng nhập) -->
                    <?php if ($isLoggedIn): ?>
                        <div class="mb-3">
                            <label for="feedbackTrip" class="form-label">Chuyến đi đánh giá</label>
                            <select class="form-select form-control" id="feedbackTrip" name="trip_id">
                                <?php if (empty($trips)): ?>
                                    <option value="">-- Bạn chưa có chuyến đi nào được đặt thành công --</option>
                                <?php else: ?>
                                    <option value="">-- Chọn chuyến đi đã đặt của bạn (Không bắt buộc) --</option>
                                <?php endif; ?>
                                <?php foreach ($trips as $t): ?>
                                    <?php 
                                        $formattedTime = date('H:i d/m/Y', strtotime((string) $t['departure_time']));
                                        $selectedAttr = (string) ($old['trip_id'] ?? '') === (string) $t['trip_id'] ? 'selected' : '';
                                    ?>
                                    <option value="<?= e($t['trip_id']) ?>" <?= $selectedAttr ?>>
                                        <?= e($t['from_name']) ?> &rarr; <?= e($t['to_name']) ?> (<?= e($t['bus_name']) ?> - Khởi hành: <?= $formattedTime ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="mb-3 p-3 bg-light rounded-4 text-muted border d-flex align-items-center gap-2" style="font-size: 0.9rem; line-height: 1.5;">
                            <i class="bi bi-info-circle-fill text-success fs-5 flex-shrink-0"></i>
                            <span>Bạn có thể <a href="<?= url('/login?redirect=/about') ?>" class="text-success fw-bold text-decoration-none">đăng nhập</a> để tự động điền thông tin và lựa chọn chuyến đi cụ thể cần đánh giá!</span>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="feedbackMessage" class="form-label">Ý kiến nhận xét & góp ý <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="feedbackMessage" name="message" rows="4" 
                                  placeholder="Nhập nội dung nhận xét hoặc ý kiến đóng góp của bạn về chuyến đi..." required><?= e($old['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-send-feedback w-100 py-3">
                        <i class="bi bi-send-fill me-2"></i>Gửi Đánh Giá Ngay
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bản đồ Vị trí -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="fw-bold text-dark mb-4 text-center"><i class="bi bi-map me-2 text-success"></i>Bản đồ vị trí văn phòng LobiBus</h3>
            <div class="map-wrapper">
                <iframe 
                    src="https://maps.google.com/maps?q=12%20Ph%E1%BB%91%20Ch%C3%B9a%20B%E1%BB%99c,%20%C4%90%E1%BB%91ng%20%C4%90a,%20H%C3%A0%20N%E1%BB%99i&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                    width="100%" 
                    height="450" 
                    style="border:0; display: block;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-item');
    const ratingInput = document.getElementById('ratingInput');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            ratingInput.value = value;
            
            // Cập nhật giao diện các ngôi sao sau khi click
            stars.forEach(s => {
                const sVal = s.getAttribute('data-value');
                if (parseInt(sVal) <= parseInt(value)) {
                    s.classList.remove('bi-star', 'text-muted');
                    s.classList.add('bi-star-fill', 'text-warning');
                } else {
                    s.classList.remove('bi-star-fill', 'text-warning');
                    s.classList.add('bi-star', 'text-muted');
                }
            });
        });
        
        star.addEventListener('mouseover', function() {
            const value = this.getAttribute('data-value');
            stars.forEach(s => {
                const sVal = s.getAttribute('data-value');
                if (parseInt(sVal) <= parseInt(value)) {
                    s.classList.add('text-warning');
                } else {
                    s.classList.remove('text-warning');
                }
            });
        });
        
        star.addEventListener('mouseout', function() {
            const currentRating = ratingInput.value;
            stars.forEach(s => {
                const sVal = s.getAttribute('data-value');
                if (currentRating && parseInt(sVal) <= parseInt(currentRating)) {
                    s.classList.add('text-warning');
                    s.classList.remove('text-muted');
                } else {
                    s.classList.remove('text-warning');
                    if (!s.classList.contains('bi-star-fill')) {
                        s.classList.add('text-muted');
                    }
                }
            });
        });
    });
});
</script>
