<?php
$pageCss = ['schedule.css'];
$pageJs = ['schedule.js', 'map.js'];
$locations = isset($locations) && is_array($locations) ? $locations : [];
?>

<div class="container py-5">
    <!-- Schedule Hero Banner -->
    <div class="schedule-hero text-white p-4 p-md-5 mb-4">
        <div class="row align-items-center g-4 position-relative z-3">
            <div class="col-lg-7">
                <span class="badge bg-teal-subtle text-success-light mb-3 px-3 py-2 fs-7 fw-bold" style="background-color: rgba(255, 255, 255, 0.15); color: #2dd4bf; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="bi bi-clock-fill me-1"></i> Lịch Trình Xe Khách LobiBus
                </span>
                <h1 class="display-5 fw-bold mb-3">Tra Cứu Giờ Chạy & Giá Vé Từng Tuyến</h1>
                <p class="lead mb-0 text-white-75">
                    Hệ thống cung cấp lịch trình chạy xe tự động gộp theo tuyến đường rõ ràng, logic. Giúp quý khách dễ dàng tra cứu giờ đi, loại xe và đặt vé nhanh chóng trong 1 nốt nhạc.
                </p>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-pill text-center h-100">
                            <i class="bi bi-signpost-2-fill mb-2 d-inline-block"></i>
                            <div class="h3 fw-bold mb-0 text-white" id="statRouteCount">-</div>
                            <small class="text-white-50">Tuyến đường</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-pill text-center h-100">
                            <i class="bi bi-bus-front-fill mb-2 d-inline-block"></i>
                            <div class="h3 fw-bold mb-0 text-white" id="statTripCount">-</div>
                            <small class="text-white-50">Chuyến chạy mở bán</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-pill text-center h-100">
                            <i class="bi bi-shield-check mb-2 d-inline-block"></i>
                            <div class="h5 fw-bold mb-0 text-white">An Toàn</div>
                            <small class="text-white-50">Chất lượng 5★</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-pill text-center h-100">
                            <i class="bi bi-percent mb-2 d-inline-block"></i>
                            <div class="h5 fw-bold mb-0 text-white">Giá Tối Ưu</div>
                            <small class="text-white-50">Nhiều ưu đãi</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section Panel -->
    <div class="search-panel">
        <h5 class="fw-bold mb-4" style="color: #0f766e;">
            <i class="bi bi-search me-2"></i>Tìm kiếm lịch trình nhanh
        </h5>
        
        <form id="tripSearchForm" class="row g-3 align-items-center" data-auto-load="1">
            <!-- Origin Location -->
            <div class="col-md-3">
                <div class="form-floating">
                    <select name="from" id="fromLocationSelect" class="form-select border-2 border-light-subtle rounded-3">
                        <option value="">-- Tất cả điểm đi --</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= e($loc['name']) ?>" <?= (($_GET['from'] ?? '') === ($loc['name'] ?? '')) ? 'selected' : '' ?>><?= e($loc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="fromLocationSelect" class="text-secondary fw-semibold">Điểm đi</label>
                </div>
            </div>
            
            <!-- Swap Button -->
            <div class="col-md-1 col-sm-12 text-center my-0">
                <button type="button" id="swapLocationsBtn" class="swap-btn shadow-sm" title="Đảo ngược điểm đi/đến">
                    <i class="bi bi-arrow-left-right"></i>
                </button>
            </div>
            
            <!-- Destination Location -->
            <div class="col-md-3">
                <div class="form-floating">
                    <select name="to" id="toLocationSelect" class="form-select border-2 border-light-subtle rounded-3">
                        <option value="">-- Tất cả điểm đến --</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= e($loc['name']) ?>" <?= (($_GET['to'] ?? '') === ($loc['name'] ?? '')) ? 'selected' : '' ?>><?= e($loc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="toLocationSelect" class="text-secondary fw-semibold">Điểm đến</label>
                </div>
            </div>
            
            <!-- Date Picker -->
            <div class="col-md-3">
                <div class="form-floating">
                    <input name="date" type="date" id="dateInput" class="form-control border-2 border-light-subtle rounded-3" value="<?= e($_GET['date'] ?? '') ?>">
                    <label for="dateInput" class="text-secondary fw-semibold">Ngày đi</label>
                </div>
            </div>
            
            <!-- Search Button -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100 rounded-3 py-3 fw-bold fs-6 shadow-sm btn-submit-search" style="background-color: #0f766e; border-color: #0f766e;">
                    <i class="bi bi-search-heart me-2"></i>Tìm Lịch
                </button>
            </div>
        </form>
    </div>

    <!-- Advanced Filter and Sorting Dashboard -->
    <div class="filter-card">
        <div class="row g-4 align-items-center">
            <!-- Filter by departure times -->
            <div class="col-lg-5 col-md-12">
                <div class="filter-section-title">
                    <i class="bi bi-hourglass-split me-1 text-teal"></i>Khung giờ chạy
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <label class="time-pill-btn">
                        <input type="checkbox" value="morning">
                        <i class="bi bi-brightness-high"></i> Sáng (00:00 - 12:00)
                    </label>
                    <label class="time-pill-btn">
                        <input type="checkbox" value="afternoon">
                        <i class="bi bi-cloud-sun"></i> Chiều (12:00 - 18:00)
                    </label>
                    <label class="time-pill-btn">
                        <input type="checkbox" value="evening">
                        <i class="bi bi-moon-stars"></i> Tối (18:00 - 24:00)
                    </label>
                </div>
            </div>
            
            <!-- Filter by vehicle types -->
            <div class="col-lg-4 col-md-8 col-sm-12">
                <div class="filter-section-title">
                    <i class="bi bi-bus-front me-1 text-teal"></i>Dòng xe LobiBus
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <label class="type-pill-btn">
                        <input type="checkbox" value="vip">
                        <i class="bi bi-gem"></i> VIP Limousine
                    </label>
                    <label class="type-pill-btn">
                        <input type="checkbox" value="sleeper">
                        <i class="bi bi-moon-stars"></i> Giường nằm
                    </label>
                    <label class="type-pill-btn">
                        <input type="checkbox" value="seat">
                        <i class="bi bi-person-workspace"></i> Ghế ngồi
                    </label>
                </div>
            </div>
            
            <!-- Sort by Selector -->
            <div class="col-lg-3 col-md-4 col-sm-12 text-md-end">
                <div class="filter-section-title text-start text-lg-end">
                    <i class="bi bi-sort-down me-1 text-teal"></i>Sắp xếp lịch trình
                </div>
                <select id="sortBySelect" class="form-select border-light-subtle rounded-3 fw-semibold">
                    <option value="time-asc" selected>Giờ xuất phát: Sớm nhất</option>
                    <option value="time-desc">Giờ xuất phát: Muộn nhất</option>
                    <option value="price-asc">Giá vé: Từ thấp đến cao</option>
                    <option value="price-desc">Giá vé: Từ cao đến thấp</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Grouped Route Results Container -->
    <div id="tripResults" class="row g-4 mb-4">
        <!-- JS inserts route cards dynamically here -->
    </div>

    <!-- Bottom Actions -->
    <div class="d-flex justify-content-start align-items-center border-top pt-4 mt-4">
        <a href="<?= url('/') ?>" class="btn btn-outline-secondary px-4 py-2 rounded-3 text-decoration-none fw-semibold">
            <i class="bi bi-arrow-left me-2"></i>Quay lại trang chủ
        </a>
    </div>
</div>