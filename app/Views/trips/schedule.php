<?php
$pageJs = ['trip-search.js', 'map.js'];
$locations = isset($locations) && is_array($locations) ? $locations : [];
?>
<section class="container py-5">
    <div class="p-4 p-md-5 mb-4 rounded-4 text-white" style="background: linear-gradient(135deg, #0f766e 0%, #134e4a 55%, #1f2937 100%);">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-light text-dark mb-3">Lịch trình chuyến đi</span>
                <h1 class="display-5 fw-bold mb-3">Tra cứu tất cả các chuyến xe đang được lên lịch</h1>
                <p class="lead mb-0 text-white-75">Xem nhanh điểm đi, điểm đến, giờ khởi hành, giờ tới, loại xe bus và số ghế còn lại của từng chuyến.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="#tripSearchForm" class="btn btn-light btn-lg px-4">Xem lịch trình</a>
            </div>
        </div>
    </div>
    <form id="tripSearchForm" class="row g-3 my-4" data-auto-load="1">
        <div class="col-md-3">
            <select name="from" class="form-select">
                <option value="">-- Chọn điểm đi --</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= e($loc['name']) ?>" <?= (($_GET['from'] ?? '') === ($loc['name'] ?? '')) ? 'selected' : '' ?>><?= e($loc['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="to" class="form-select">
                <option value="">-- Chọn điểm đến --</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= e($loc['name']) ?>" <?= (($_GET['to'] ?? '') === ($loc['name'] ?? '')) ? 'selected' : '' ?>><?= e($loc['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3"><input name="date" type="date" class="form-control" value="<?= e($_GET['date'] ?? '') ?>"></div>
        <div class="col-md-3"><button class="btn btn-success w-100">Tìm kiếm</button></div>
    </form>
    <div id="tripResults" class="row g-3"></div>
    <a href="/">Quay về trang chủ</a>
</section>