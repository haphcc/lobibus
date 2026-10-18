<?php
$pageCss = ['datchuyen.css'];
$pageJs = ['trip-search.js', 'recommendation.js', 'chatbot.js'];
?>
<section class="booking-hero">
    <div class="booking-form-wrapper">
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
                        <option value="Hà Nội">Hà Nội</option>
                        <option value="Hải Phòng">Hải Phòng</option>
                        <option value="Nam Định">Nam Định</option>
                        <option value="Ninh Bình">Ninh Bình</option>
                        <option value="Thanh Hóa">Thanh Hóa</option>
                    </select>
                </div>
                <div class="form-col-half">
                    <label class="form-label" for="to">Điểm đến</label>
                    <select id="to" name="to" class="form-select form-select-lg" required>
                        <option value="Hải Phòng">Hải Phòng</option>
                        <option value="Hà Nội">Hà Nội</option>
                        <option value="Ninh Bình">Ninh Bình</option>
                        <option value="Thanh Hóa">Thanh Hóa</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label class="form-label" for="departDate">Ngày đi</label>
                    <input type="date" id="departDate" name="date" class="form-control form-control-lg" required>
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
</section>
<section class="container my-5">
    <h2 class="mb-4">Chuyến gợi ý</h2>
    <div id="tripResults" class="row g-4"></div>
</section>
