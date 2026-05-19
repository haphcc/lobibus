<?php
$pageCss = ['datchuyen.css'];
$pageJs = ['trip-search.js', 'recommendation.js', 'chatbot.js'];
?>
<section class="booking-hero">
    <div class="booking-form-wrapper">
        <div class="container">
        <form id="tripSearchForm" class="booking-form">
            <div class="trip-type-selector">
                <div class="trip-type-group">
                    <input type="radio" class="btn-check" name="tripType" id="oneWay" value="oneway" checked>
                    <label class="trip-type-label" for="oneWay">Một chiều</label>
                    <input type="radio" class="btn-check" name="tripType" id="roundTrip" value="roundtrip">
                    <label class="trip-type-label" for="roundTrip">Khứ hồi</label>
                </div>
            </div>
            <div class="form-row-full">
                <div class="form-col-half">
                    <label class="form-label" for="from">Điểm đi</label>
                    <select id="from" name="from" class="form-select form-select-lg" required>
                        <option value="">-- Chọn điểm đi --</option>
                        <?php foreach ($locations as $loc): ?>
                        <option value="<?= e($loc['name']) ?>"><?= e($loc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-col-half">
                    <label class="form-label" for="to">Điểm đến</label>
                    <select id="to" name="to" class="form-select form-select-lg" required>
                        <option value="">-- Chọn điểm đến --</option>
                        <?php foreach ($locations as $loc): ?>
                        <option value="<?= e($loc['name']) ?>"><?= e($loc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label class="form-label" for="departDate">Ngày đi</label>
                    <input type="date" id="departDate" name="date" class="form-control form-control-lg" required>
                </div>
                <div class="form-col" id="returnDateWrapper" style="display:none;">
                    <label class="form-label" for="returnDate">Ngày về</label>
                    <input type="date" id="returnDate" name="return_date" class="form-control form-control-lg" disabled>
                </div>
                <div class="form-col">
                    <label class="form-label" for="seats">Số ghế</label>
                    <select id="seats" name="seats" class="form-select form-select-lg">
                        <option value="1">1 hành khách</option>
                        <option value="2">2 hành khách</option>
                        <option value="3">3 hành khách</option>
                    </select>
                </div>
                <div class="form-col-search">
                    <button type="submit" class="btn btn-search"><span class="fw-bold">TÌM KIẾM</span></button>
                </div>
            </div>
        </form>
        </div>
    </div>
</section>
<section class="container my-5">
    <h2 class="mb-4">Chuyến gợi ý</h2>
</section>
<section class="booking-hero">
    <div class="booking-form-wrapper">
        <div class="container">
            <div id="tripResults" class="row g-4"></div>
            <div id="selectedTickets" class="mt-4">
                <!-- Selected tickets will be rendered here by JS -->
            </div>
        </div>
    </div>
</section>
