<?php
$pageCss = ['recommendation.css'];
$pageJs = ['recommendation.js'];
?>
<section class="recommendation-page">
    <div class="container">


        <div class="recommendation-header mt-2">
            <div>
                <span class="section-kicker">Gợi ý thông minh</span>
                <h1>Chuyến xe phù hợp cho bạn</h1>
                <p>Danh sách được AI tổng hợp dựa trên thói quen đặt vé, giá cả, và lịch trình tối ưu nhất.</p>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Vertical Filters Sidebar -->
            <aside class="col-lg-3 mb-4">
                <div class="filter-sidebar shadow-sm">
                    <h5 class="filter-title"><i class="bi bi-funnel-fill me-2 text-success"></i>Bộ lọc chuyến</h5>
                    
                    <div class="filter-section mt-3">
                        <h6 class="filter-subtitle text-muted mb-3">Tiêu chí gợi ý</h6>
                        <div class="recommendation-toolbar-vertical">
                            <button class="recommendation-filter active w-100 text-start" type="button" data-filter="all">
                                <i class="bi bi-grid me-2"></i>Tất cả chuyến
                            </button>
                            <button class="recommendation-filter w-100 text-start" type="button" data-filter="Chuyến rẻ nhất">
                                <i class="bi bi-tag me-2"></i>Rẻ nhất
                            </button>
                            <button class="recommendation-filter w-100 text-start" type="button" data-filter="Khởi hành sớm nhất">
                                <i class="bi bi-clock me-2"></i>Sớm nhất
                            </button>
                            <button class="recommendation-filter w-100 text-start" type="button" data-filter="Còn nhiều ghế nhất">
                                <i class="bi bi-person-check me-2"></i>Nhiều ghế nhất
                            </button>
                            <button class="recommendation-filter w-100 text-start" type="button" data-filter="Phổ biến nhất">
                                <i class="bi bi-fire me-2"></i>Phổ biến nhất
                            </button>
                        </div>
                    </div>
                    
                    <!-- Decorative / Layout complete filters -->
                    <hr class="my-4 text-muted">
                    <div class="filter-section">
                        <h6 class="filter-subtitle text-muted mb-3">Mức giá</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="price1">
                            <label class="form-check-label" for="price1">Dưới 200,000đ</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="price2">
                            <label class="form-check-label" for="price2">200,000đ - 500,000đ</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="price3">
                            <label class="form-check-label" for="price3">Trên 500,000đ</label>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">
                    <div class="filter-section">
                        <h6 class="filter-subtitle text-muted mb-3">Giờ khởi hành</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="time1">
                            <label class="form-check-label" for="time1">Sáng (06:00 - 12:00)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="time2">
                            <label class="form-check-label" for="time2">Chiều (12:00 - 18:00)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="time3">
                            <label class="form-check-label" for="time3">Tối (18:00 - 24:00)</label>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Recommendation Grid -->
            <div class="col-lg-9">
                <div id="recommendationList" class="recommendation-grid-2col">
                    <div class="recommendation-loading">
                        <div class="spinner-border text-success" role="status"></div>
                        <div class="mt-2 text-muted">Đang tìm chuyến đi phù hợp nhất với bạn...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
