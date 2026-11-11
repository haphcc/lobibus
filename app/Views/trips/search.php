<?php
$pageCss = ['schedule.css'];
$pageJs = ['trip-search.js', 'map.js'];
$locations = isset($locations) && is_array($locations) ? $locations : [];
?>

<style>
/* Swap Button & Custom labels matching homepage booking elements */
.trip-type-label {
    border: 2px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.6rem 1.2rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
    color: #475569;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-block;
}

.btn-check:checked + .trip-type-label {
    background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    border-color: #0f766e;
    color: white;
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2);
}

.trip-type-label:hover {
    border-color: #0f766e;
    color: #0f766e;
}

.trip-schedule-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.6rem 1.2rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
    color: #475569;
    font-weight: 600;
    font-size: 0.95rem;
    line-height: 1;
    text-decoration: none;
    white-space: nowrap;
}

.trip-schedule-link:hover {
    border-color: #0f766e;
    color: #0f766e;
}

.swap-btn-overlay {
    position: absolute;
    right: -18px;
    top: 50%;
    transform: translateY(-50%);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #0f766e;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
}

.swap-btn-overlay:hover {
    background: #0f766e;
    color: #ffffff;
    border-color: #0f766e;
    transform: translateY(-50%) rotate(180deg);
}

@media (max-width: 991.98px) {
    .swap-btn-overlay {
        right: 50%;
        top: calc(100% - 18px);
        transform: translateX(50%) rotate(90deg);
    }
    .swap-btn-overlay:hover {
        transform: translateX(50%) rotate(270deg);
    }
}

/* Header & Date Strip custom styles */
.search-header-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2ece7;
    box-shadow: 0 4px 20px rgba(15, 118, 110, 0.02);
}
.city-name-large {
    font-size: 1.8rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.01em;
}
.date-strip-wrapper {
    background: #f8fafc;
    border: 1px solid #e2ece7;
    border-radius: 20px;
    padding: 15px 25px;
    box-shadow: 0 4px 20px rgba(15, 118, 110, 0.03);
}
.date-nav-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.date-nav-btn:hover {
    background: #0f766e;
    color: #ffffff;
    border-color: #0f766e;
    box-shadow: 0 4px 10px rgba(15, 118, 110, 0.2);
}
.date-items-container {
    gap: 12px;
    overflow-x: auto;
    scrollbar-width: none;
}
.date-items-container::-webkit-scrollbar {
    display: none;
}
.date-item {
    flex: 1;
    min-width: 130px;
    text-align: center;
    padding: 12px 10px;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: transparent;
    border: 1px solid transparent;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.date-item:hover {
    background: rgba(15, 118, 110, 0.05);
}
.date-item-dow {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 2px;
}
.date-item-day {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
}
.date-item-price {
    font-size: 0.75rem;
    color: #0f766e;
    font-weight: 700;
    margin-top: 4px;
}
.date-item.active {
    background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    border-color: #0f766e;
    box-shadow: 0 8px 24px rgba(15, 118, 110, 0.2);
    transform: scale(1.06);
    position: relative;
}
.date-item.active .date-item-dow {
    color: rgba(255, 255, 255, 0.8);
}
.date-item.active .date-item-day {
    color: #ffffff;
}
.date-item.active .date-item-price {
    color: #2dd4bf;
    font-weight: 800;
    font-size: 0.8rem;
}
@media (max-width: 767.98px) {
    .date-strip-wrapper {
        padding: 10px 10px;
    }
    .date-item {
        min-width: 100px;
        padding: 8px 5px;
    }
    .date-item-dow {
        font-size: 0.75rem;
    }
    .date-item-day {
        font-size: 0.95rem;
    }
    .date-item-price {
        font-size: 0.7rem;
    }
}
</style>

<div class="container-fluid px-lg-5 py-5" style="max-width: 1500px; margin: 0 auto;">
    <!-- Search Section Panel -->
    <div class="search-panel shadow-sm">
        <h5 class="fw-bold mb-4" style="color: #0f766e;">
            <i class="bi bi-search-heart me-2"></i>Tìm kiếm hành trình đặt vé
        </h5>
        
        <form id="tripSearchForm" class="row g-3 align-items-center">
            <!-- Trip Type Selector -->
            <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                <div class="trip-type-group">
                    <input type="radio" class="btn-check" name="tripType" id="oneWay" value="oneway" checked>
                    <label class="trip-type-label" for="oneWay">Một chiều</label>
                    <input type="radio" class="btn-check" name="tripType" id="roundTrip" value="roundtrip">
                    <label class="trip-type-label" for="roundTrip">Khứ hồi</label>
                </div>
                <a href="<?= url('/trips/schedule') ?>" class="trip-schedule-link ms-auto">
                    <i class="bi bi-calendar3 me-1"></i> Xem lịch trình
                </a>
            </div>

            <!-- Origin Location -->
            <div class="col-lg-2 col-md-6 position-relative mb-md-2 mb-lg-0">
                <div class="form-floating">
                    <select name="from" id="fromLocationSelect" class="form-select border-2 border-light-subtle rounded-3" required>
                        <option value="">-- Chọn điểm đi --</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= e($loc['name']) ?>"><?= e($loc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="fromLocationSelect" class="text-secondary fw-semibold">Điểm đi</label>
                </div>
                <!-- Swap Button Overlay -->
                <button type="button" id="swapLocationsBtn" class="swap-btn-overlay shadow-sm" title="Đảo ngược điểm đi/đến">
                    <i class="bi bi-arrow-left-right"></i>
                </button>
            </div>
            
            <!-- Destination Location -->
            <div class="col-lg-2 col-md-6 mb-md-2 mb-lg-0">
                <div class="form-floating">
                    <select name="to" id="toLocationSelect" class="form-select border-2 border-light-subtle rounded-3" required>
                        <option value="">-- Chọn điểm đến --</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= e($loc['name']) ?>"><?= e($loc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="toLocationSelect" class="text-secondary fw-semibold">Điểm đến</label>
                </div>
            </div>
            
            <!-- Date Picker -->
            <div class="col-lg-2 col-md-4">
                <div class="form-floating">
                    <input name="date" type="date" id="dateInput" class="form-control border-2 border-light-subtle rounded-3" required>
                    <label for="dateInput" class="text-secondary fw-semibold">Ngày đi</label>
                </div>
            </div>

            <!-- Return Date Picker -->
            <div class="col-lg-2 col-md-4" id="returnDateWrapper">
                <div class="form-floating">
                    <input name="return_date" type="date" id="returnDate" class="form-control border-2 border-light-subtle rounded-3" disabled>
                    <label for="returnDate" class="text-secondary fw-semibold">Ngày về</label>
                </div>
            </div>
            
            <!-- Seats Selector -->
            <div class="col-lg-2 col-md-4">
                <div class="form-floating">
                    <select name="seats" id="seatsSelect" class="form-select border-2 border-light-subtle rounded-3" required>
                        <option value="1">1 hành khách</option>
                        <option value="2">2 hành khách</option>
                        <option value="3">3 hành khách</option>
                        <option value="4">4 hành khách</option>
                        <option value="5">5 hành khách</option>
                    </select>
                    <label for="seatsSelect" class="text-secondary fw-semibold">Số lượng ghế</label>
                </div>
            </div>

            <!-- Search Button -->
            <div class="col-lg-2 col-md-12">
                <button type="submit" class="btn btn-success w-100 rounded-3 py-3 fw-bold fs-6 shadow-sm btn-submit-search" style="background-color: #0f766e; border-color: #0f766e;">
                    <i class="bi bi-search me-2"></i>Tìm Chuyến
                </button>
            </div>
        </form>
    </div>

    <!-- Origin-Destination Header Card (Teal Theme with normal names and trip details) -->
    <div class="search-header-card text-center py-4 mb-4 d-none" id="searchHeaderCard">
        <div class="d-flex flex-column align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center gap-4 flex-wrap">
                <div class="city-name-large" id="originCityName">-</div>
                <div class="d-flex flex-column align-items-center justify-content-center px-2">
                    <div class="arrow-line-container">
                        <svg width="48" height="12" viewBox="0 0 48 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 6H44.5M44.5 6L39.5 1M44.5 6L39.5 11" stroke="#0f766e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                <div class="city-name-large" id="destinationCityName">-</div>
            </div>
            <div class="mt-2 d-flex gap-2 justify-content-center align-items-center flex-wrap" id="tripDetailsBadges">
                <span class="badge bg-teal-subtle text-teal px-3 py-2 rounded-pill fw-bold" id="badgeTripType" style="color: #0f766e !important; background-color: #e6f4f2 !important; border: 1px solid #cbd5e1;">Một chiều</span>
                <span class="badge bg-teal-subtle text-teal px-3 py-2 rounded-pill fw-bold d-none" id="badgeTripDirection" style="color: #0f766e !important; background-color: #e6f4f2 !important; border: 1px solid #cbd5e1;">Chiều đi</span>
            </div>
        </div>
    </div>

    <!-- Date Strip Slider Carousel -->
    <div class="date-strip-wrapper my-4 d-none" id="dateStripWrapper">
        <div class="d-flex align-items-center justify-content-center gap-2">
            <!-- Prev button -->
            <button type="button" class="date-nav-btn prev-btn" id="datePrevBtn" title="Ngày trước">
                <i class="bi bi-chevron-left"></i>
            </button>

            <!-- Date items row -->
            <div class="date-items-container d-flex align-items-center justify-content-between flex-grow-1" id="dateItemsRow">
                <!-- Date tabs will be dynamically rendered here by JS -->
            </div>

            <!-- Next button -->
            <button type="button" class="date-nav-btn next-btn" id="dateNextBtn" title="Ngày sau">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Advanced Filter and Sorting Dashboard (Hidden initially) -->
    <div class="filter-card" id="filterCardWrapper" style="display: none;">
        <div class="row g-4 align-items-center">
            <!-- Filter by departure times -->
            <div class="col-lg-5 col-md-12">
                <div class="filter-section-title">
                    <i class="bi bi-hourglass-split me-1 text-teal" style="color: #0f766e;"></i>Khung giờ chạy
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
                    <i class="bi bi-bus-front me-1 text-teal" style="color: #0f766e;"></i>Dòng xe LobiBus
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
                    <i class="bi bi-sort-down me-1 text-teal" style="color: #0f766e;"></i>Sắp xếp chuyến đi
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

    <!-- Selected Tickets Section -->
    <div id="selectedTickets" class="mt-4">
        <!-- Selected tickets will be rendered here by JS -->
    </div>
</div>
