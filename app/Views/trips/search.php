<?php $pageJs = ['trip-search.js', 'map.js']; ?>
<section class="container py-5">
    <div class="p-4 p-md-5 mb-4 rounded-4 text-white" style="background: linear-gradient(135deg, #1f2937 0%, #0f766e 100%);">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-light text-dark mb-3">Đặt chuyến</span>
                <h1 class="display-5 fw-bold mb-3">Tìm chuyến và đặt vé nhanh</h1>
                <p class="lead mb-0 text-white-75">Chọn điểm đi, điểm đến và ngày khởi hành để xem các chuyến xe phù hợp trước khi đặt vé.</p>
            </div>
        </div>
    </div>
    <form id="tripSearchForm" class="row g-3 my-4">
        <div class="col-md-3"><input name="from" class="form-control" placeholder="Điểm đi" value="<?= e($_GET['from'] ?? '') ?>"></div>
        <div class="col-md-3"><input name="to" class="form-control" placeholder="Điểm đến" value="<?= e($_GET['to'] ?? '') ?>"></div>
        <div class="col-md-3"><input name="date" type="date" class="form-control" value="<?= e($_GET['date'] ?? '') ?>"></div>
        <div class="col-md-3"><button class="btn btn-success w-100">Tìm kiếm</button></div>
    </form>
    <div id="tripResults" class="row g-3"></div>
    <a href="/">Quay về trang chủ</a>
</section>
