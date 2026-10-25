<?php
$pageCss = ['recommendation.css'];
$pageJs = ['recommendation.js'];
?>
<section class="recommendation-page">
    <div class="container">
        <div class="recommendation-header">
            <div>
                <span class="section-kicker">Gợi ý thông minh</span>
                <h1>Chuyến xe phù hợp cho bạn</h1>
                <p>Danh sách được tổng hợp từ giá vé, thời gian khởi hành, số ghế trống và lịch sử đặt vé.</p>
            </div>
            <a class="btn btn-outline-success" href="<?= url('/trips/search') ?>">Tìm chuyến khác</a>
        </div>

        <div class="recommendation-toolbar">
            <button class="recommendation-filter active" type="button" data-filter="all">Tất cả</button>
            <button class="recommendation-filter" type="button" data-filter="Chuyến rẻ nhất">Rẻ nhất</button>
            <button class="recommendation-filter" type="button" data-filter="Khởi hành sớm nhất">Sớm nhất</button>
            <button class="recommendation-filter" type="button" data-filter="Còn nhiều ghế nhất">Nhiều ghế</button>
            <button class="recommendation-filter" type="button" data-filter="Phổ biến nhất">Phổ biến</button>
        </div>

        <div id="recommendationList" class="recommendation-grid">
            <div class="recommendation-loading">Đang tải gợi ý chuyến xe...</div>
        </div>
    </div>
</section>
