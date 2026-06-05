<?php
$pageCss = ['schedule.css'];
$pageJs = ['trip-search.js'];
$locations = isset($locations) && is_array($locations) ? $locations : [];
$featuredTrips = isset($featuredTrips) && is_array($featuredTrips) ? $featuredTrips : [];
$featuredNews = isset($featuredNews) && is_array($featuredNews) ? $featuredNews : [];
$featuredPromotions = isset($featuredPromotions) && is_array($featuredPromotions) ? $featuredPromotions : [];

// Helper function to get bus type info in PHP (matches trip-search.js)
if (!function_exists('getBusTypeInfo')) {
    function getBusTypeInfo($busName) {
        $name = mb_strtolower($busName ?? '', 'UTF-8');
        if (strpos($name, 'limousine') !== false || strpos($name, 'vip') !== false || strpos($name, 'luxury') !== false) {
            return ['id' => 'vip', 'label' => 'VIP Limousine', 'badge' => 'badge-vip', 'icon' => 'bi-gem'];
        } elseif (strpos($name, 'giường') !== false || strpos($name, 'sleeper') !== false || strpos($name, 'nằm') !== false) {
            return ['id' => 'sleeper', 'label' => 'Giường nằm', 'badge' => 'badge-sleeper', 'icon' => 'bi-moon-stars'];
        } else {
            return ['id' => 'seat', 'label' => 'Ghế ngồi', 'badge' => 'badge-seating', 'icon' => 'bi-person-workspace'];
        }
    }
}
?>

<style>
/* Custom overrides to match teal theme for home page specific booking elements */
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

/* Overlay Swap Button Styling */
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
.date-item.active {
    background: #ffffff;
    border-color: #0f766e;
    box-shadow: 0 8px 16px rgba(15, 118, 110, 0.08);
    transform: translateY(-2px);
}
.date-item-dow {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 2px;
}
.date-item.active .date-item-dow {
    color: #0f766e;
}
.date-item-day {
    font-size: 1.05rem;
    font-weight: 800;
    color: #1e293b;
}
.date-item.active .date-item-day {
    color: #0f766e;
}
.date-item-price {
    font-size: 0.7rem;
    font-weight: 600;
    color: #94a3b8;
    margin-top: 4px;
}
.date-item.active .date-item-price {
    color: #2dd4bf;
}

/* Teal Outline Button */
.btn-outline-teal {
    color: #0f766e;
    border-color: #0f766e;
    background: transparent;
    transition: all 0.2s ease;
}
.btn-outline-teal:hover {
    color: #ffffff;
    background-color: #0f766e;
    border-color: #0f766e;
    box-shadow: 0 4px 10px rgba(15, 118, 110, 0.2);
}

/* Selected Tickets Premium Styling */
#selectedTickets .card {
    border: 1px solid #e2ece7;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(15, 118, 110, 0.06);
    overflow: hidden;
}

#selectedTickets .card-title {
    color: #0f766e;
    font-weight: 700;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

#selectedTickets .list-group-item {
    border: 1px solid #f1f5f9;
    margin-bottom: 8px;
    border-radius: 8px !important;
    background: #f8fafc;
    transition: all 0.2s ease;
}

#selectedTickets .list-group-item:hover {
    border-color: #cbd5e1;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

.home-news-section {
    margin-top: 4rem;
    padding-top: 2.5rem;
    border-top: 1px solid #e2ece7;
}

.home-trip-section {
    margin-top: 4rem;
    padding-top: 2.5rem;
    border-top: 1px solid #e2ece7;
}

.section-kicker {
    display: inline-block;
    background: #e6f4f2;
    color: #0f766e;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 6px 12px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.home-section-header {
    margin-bottom: 30px;
}

.home-section-header-inner {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 20px;
}

.home-section-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px 0;
}

.home-section-subtitle {
    font-size: 1rem;
    color: #64748b;
    margin: 0;
}

.home-view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #0f766e;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    font-weight: 700;
    padding: 10px 22px;
    border-radius: 99px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.25s ease;
}

.home-view-all-btn:hover {
    color: #ffffff;
    background: #0f766e;
    border-color: #0f766e;
    box-shadow: 0 8px 16px rgba(15, 118, 110, 0.15);
    transform: translateY(-2px);
}

.home-view-all-btn i {
    font-size: 1.1rem;
    transition: transform 0.2s ease;
}

.home-view-all-btn:hover i {
    transform: translateX(4px);
}

/* Featured Trip Premium Card Styling */
.home-trip-card {
    background: #ffffff;
    border: 1px solid #e2ece7;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15, 118, 110, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.home-trip-card:hover {
    transform: translateY(-5px);
    border-color: #0f766e;
    box-shadow: 0 20px 40px rgba(15, 118, 110, 0.12);
}

.home-trip-media {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #ffffff; /* Thêm background màu trắng để tránh khoảng trống subpixel */
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
}

.home-trip-media::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 35%;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, #ffffff 100%);
    pointer-events: none;
    z-index: 1;
}

.home-trip-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
}

.home-trip-card:hover .home-trip-media img {
    transform: scale(1.08) translate3d(0, 0, 0);
}

.home-trip-price-pill {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(15, 118, 110, 0.92);
    backdrop-filter: blur(4px);
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 99px;
    font-weight: 700;
    font-size: 0.9rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 2;
}

.home-trip-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.home-trip-route {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.home-trip-route i {
    color: #0f766e;
}

.home-trip-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    color: #475569;
    flex-grow: 1;
}

.home-trip-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.home-trip-meta-item i {
    font-size: 1.1rem;
    color: #0f766e;
}

/* Loại bỏ hoàn toàn đường kẻ ngang (underline) trên mọi trạng thái hover của thẻ chứa */
.home-trip-card a,
.home-trip-card a:hover,
.home-trip-card a:focus,
.home-trip-card:hover a,
.home-trip-card:hover .home-trip-action-btn,
.home-trip-action-btn,
.home-trip-action-btn:hover {
    text-decoration: none !important;
}

.home-trip-action-btn {
    background: #e6f4f2;
    color: #0f766e;
    border: none;
    font-weight: 700;
    padding: 10px;
    border-radius: 12px;
    text-align: center;
    transition: all 0.2s ease;
    display: block;
    width: 100%;
}

.home-trip-card:hover .home-trip-action-btn {
    background: #0f766e;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2);
}

.home-trip-empty {
    border: 1px dashed #bad1c6;
    border-radius: 16px;
    background: #fff;
    color: #64748b;
    padding: 1.25rem;
    text-align: center;
}

/* Featured News Premium Card Styling */
.home-news-card {
    background: #ffffff;
    border: 1px solid #e2ece7;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15, 118, 110, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.home-news-card:hover {
    transform: translateY(-5px);
    border-color: #0f766e;
    box-shadow: 0 20px 40px rgba(15, 118, 110, 0.12);
}

.home-news-media {
    position: relative;
    height: 190px;
    overflow: hidden;
}

.home-news-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.home-news-card:hover .home-news-media img {
    transform: scale(1.08);
}

.home-news-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #0f766e;
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    z-index: 2;
}

.home-news-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.home-news-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 10px;
}

.home-news-meta i {
    color: #0f766e;
}

.home-news-title {
    font-size: 1.08rem;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 10px;
}

.home-news-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s ease;
}

.home-news-title a:hover {
    color: #0f766e;
}

.home-news-summary {
    color: #475569;
    font-size: 0.92rem;
    line-height: 1.55;
    margin-bottom: 20px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-grow: 1;
}

.home-news-link {
    color: #0f766e;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: gap 0.2s ease;
    align-self: flex-start;
}

.home-news-link:hover {
    gap: 10px;
    color: #115e59;
}

@media (max-width: 767.98px) {
    .home-section-header-inner {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .home-view-all-btn {
        width: 100%;
        justify-content: center;
    }
}

/* Premium Carousel Custom Styles */
#homeCarousel {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 12px 36px rgba(15, 118, 110, 0.15);
}
.max-width-600 {
    max-width: 600px;
}
.carousel-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, rgba(15, 118, 110, 0.3) 0%, rgba(15, 118, 110, 0.8) 100%);
    z-index: 1;
}
#homeCarousel .carousel-caption {
    z-index: 2;
    left: 10%;
    right: 10%;
    bottom: 12%;
}
#homeCarousel .carousel-indicators [data-bs-target] {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-left: 6px;
    margin-right: 6px;
    background-color: #ffffff;
    opacity: 0.5;
    transition: all 0.3s ease;
}
#homeCarousel .carousel-indicators .active {
    opacity: 1;
    background-color: #2dd4bf;
    transform: scale(1.25);
}
#homeCarousel .carousel-control-prev {
    left: 24px;
    width: 48px;
    height: 48px;
    background-color: rgba(15, 118, 110, 0.35);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    transition: all 0.3s ease;
}
#homeCarousel .carousel-control-next {
    right: 24px;
    width: 48px;
    height: 48px;
    background-color: rgba(15, 118, 110, 0.35);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    transition: all 0.3s ease;
}
#homeCarousel .carousel-control-prev:hover,
#homeCarousel .carousel-control-next:hover {
    background-color: rgba(15, 118, 110, 0.85);
    box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3);
}
@media (max-width: 767.98px) {
    #homeCarousel .carousel-inner {
        height: 320px !important;
    }
    #homeCarousel .carousel-caption {
        bottom: 8%;
        left: 6%;
        right: 6%;
    }
    #homeCarousel .carousel-caption h1 {
        font-size: 1.6rem !important;
    }
    #homeCarousel .carousel-caption p {
        font-size: 0.85rem !important;
    }
    #homeCarousel .carousel-control-prev,
    #homeCarousel .carousel-control-next {
        display: none;
    }
}

/* Reviews & Stats Showcase Section Styles */
.review-card {
    background: #ffffff;
    border: 1px solid #e2ece7;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(15, 118, 110, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.review-card:hover {
    transform: translateY(-5px);
    border-color: #0f766e;
    box-shadow: 0 20px 40px rgba(15, 118, 110, 0.1);
}
.avatar-wrapper {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}
.avatar-initials {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-weight: 700;
    font-size: 1.05rem;
}
.bg-teal-subtle {
    background-color: #e6f4f2 !important;
}
.text-success-light {
    color: #2dd4bf !important;
}
.text-teal-light {
    color: #2dd4bf;
}
.fw-extrabold {
    font-weight: 850 !important;
}
.number-item {
    transition: all 0.3s ease;
}
.number-item:hover {
    transform: scale(1.05);
}
.number-icon-wrapper {
    background-color: rgba(255, 255, 255, 0.08);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}
.number-item:hover .number-icon-wrapper {
    background-color: rgba(255, 255, 255, 0.16);
    transform: rotate(15deg);
}

/* Promotions Custom Styles */
.promo-code-badge {
    display: inline-block;
    background: #e6f4f2;
    color: #0f766e;
    font-size: 0.8rem;
    font-weight: 700;
    border: 1px dashed #0f766e;
    padding: 4px 10px;
    border-radius: 6px;
    margin-top: 8px;
    align-self: flex-start;
}
.home-promo-card {
    background: #ffffff;
    border: 1px solid #e2ece7;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15, 118, 110, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
}
.home-promo-card:hover {
    transform: translateY(-5px);
    border-color: #0f766e;
    box-shadow: 0 20px 40px rgba(15, 118, 110, 0.12);
}
.home-promo-media {
    position: relative;
    height: 190px;
    overflow: hidden;
}
.home-promo-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.home-promo-card:hover .home-promo-media img {
    transform: scale(1.08);
}
.home-promo-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #ea580c;
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    z-index: 2;
}
.home-promo-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.home-promo-title {
    font-size: 1.08rem;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 10px;
}
.home-promo-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s ease;
}
.home-promo-title a:hover {
    color: #0f766e;
}
.home-promo-summary {
    color: #475569;
    font-size: 0.92rem;
    line-height: 1.55;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-grow: 1;
}
.home-promo-link {
    color: #ea580c;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: gap 0.2s ease;
    align-self: flex-start;
}
.home-promo-link:hover {
    gap: 10px;
    color: #c2410c;
}
.home-promo-section {
    margin-top: 4rem;
    padding-top: 2.5rem;
    border-top: 1px solid #e2ece7;
}
</style>

<div class="container-fluid px-lg-5 py-5" style="max-width: 1500px; margin: 0 auto;">
    <!-- Home Hero Carousel Showcase -->
    <div id="homeCarousel" class="carousel slide carousel-fade mb-4 shadow" data-bs-ride="carousel" data-bs-interval="6000">
        <!-- Indicators/dots -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
        </div>

        <!-- The slideshow/carousel -->
        <div class="carousel-inner" style="border-radius: 20px; overflow: hidden; height: 420px; position: relative;">
            <!-- Slide 1 -->
            <div class="carousel-item active" style="height: 100%;">
                <div class="carousel-image-overlay"></div>
                <img src="<?= asset('images/news/limousine_vip.png') ?>" class="d-block w-100" style="object-fit: cover; height: 100%;" alt="VIP Limousine LobiBus">
                <div class="carousel-caption text-start d-flex flex-column justify-content-end h-100 pb-4">
                    <span class="badge bg-teal-subtle text-success-light mb-2 px-3 py-2 fs-7 fw-bold align-self-start" style="background-color: rgba(255, 255, 255, 0.15); color: #2dd4bf; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-gem me-1"></i> Dịch Vụ Đẳng Cấp 5 Sao
                    </span>
                    <h1 class="display-6 fw-bold mb-2 text-white">VIP Limousine Đẳng Cấp & Sang Trọng</h1>
                    <p class="lead mb-0 text-white-75 max-width-600">
                        Dòng xe VIP Limousine đời mới nhất với ghế massage êm ái, cổng sạc tiện dụng, wifi tốc độ cao và nước uống miễn phí suốt hành trình.
                    </p>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item" style="height: 100%;">
                <div class="carousel-image-overlay"></div>
                <img src="<?= asset('images/news/30discount.png') ?>" class="d-block w-100" style="object-fit: cover; height: 100%;" alt="Khuyến Mãi Đặt Vé">
                <div class="carousel-caption text-start d-flex flex-column justify-content-end h-100 pb-4">
                    <span class="badge bg-teal-subtle text-success-light mb-2 px-3 py-2 fs-7 fw-bold align-self-start" style="background-color: rgba(255, 255, 255, 0.15); color: #2dd4bf; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-percent me-1"></i> Ưu Đãi Cực Hấp Dẫn
                    </span>
                    <h1 class="display-6 fw-bold mb-2 text-white">Hành Trình Trọn Vẹn - Đặt Vé Dễ Dàng</h1>
                    <p class="lead mb-0 text-white-75 max-width-600">
                        Hệ thống đặt vé xe trực tuyến nhanh chóng, an toàn và tối ưu chi phí. Ưu đãi giảm ngay 30% cho khách hàng mới đặt vé lần đầu!
                    </p>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-item" style="height: 100%;">
                <div class="carousel-image-overlay"></div>
                <img src="<?= asset('images/news/green_bus.png') ?>" class="d-block w-100" style="object-fit: cover; height: 100%;" alt="Xe Khách LobiBus An Toàn">
                <div class="carousel-caption text-start d-flex flex-column justify-content-end h-100 pb-4">
                    <span class="badge bg-teal-subtle text-success-light mb-2 px-3 py-2 fs-7 fw-bold align-self-start" style="background-color: rgba(255, 255, 255, 0.15); color: #2dd4bf; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-shield-check me-1"></i> An Toàn & Lịch Thiệp
                    </span>
                    <h1 class="display-6 fw-bold mb-2 text-white">Mỗi Chuyến Đi Là Một Niềm Vui Trọn Vẹn</h1>
                    <p class="lead mb-0 text-white-75 max-width-600">
                        Đội ngũ tài xế giàu kinh nghiệm, cam kết không phóng nhanh vượt ẩu, xe khởi hành đúng giờ, mang đến sự an tâm tuyệt đối trên mọi nẻo đường.
                    </p>
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="carousel-item" style="height: 100%;">
                <div class="carousel-image-overlay"></div>
                <img src="<?= asset('images/news/sinhvien.jpg') ?>" class="d-block w-100" style="object-fit: cover; height: 100%;" alt="Ưu Đãi Học Sinh Sinh Viên">
                <div class="carousel-caption text-start d-flex flex-column justify-content-end h-100 pb-4">
                    <span class="badge bg-teal-subtle text-success-light mb-2 px-3 py-2 fs-7 fw-bold align-self-start" style="background-color: rgba(255, 255, 255, 0.15); color: #2dd4bf; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-mortarboard-fill me-1"></i> Ưu Đãi Học Đường
                    </span>
                    <h1 class="display-6 fw-bold mb-2 text-white">Đồng Hành Cùng Học Sinh Sinh Viên</h1>
                    <p class="lead mb-0 text-white-75 max-width-600">
                        Ưu đãi giảm giá vé đặc biệt dành riêng cho các bạn học sinh, sinh viên khi đặt vé xe khách trực tuyến. Tiếp sức trên con đường học vấn!
                    </p>
                </div>
            </div>

            <!-- Slide 5 -->
            <div class="carousel-item" style="height: 100%;">
                <div class="carousel-image-overlay"></div>
                <img src="<?= asset('images/news/secure_payment.png') ?>" class="d-block w-100" style="object-fit: cover; height: 100%;" alt="Thanh Toán An Toàn">
                <div class="carousel-caption text-start d-flex flex-column justify-content-end h-100 pb-4">
                    <span class="badge bg-teal-subtle text-success-light mb-2 px-3 py-2 fs-7 fw-bold align-self-start" style="background-color: rgba(255, 255, 255, 0.15); color: #2dd4bf; border: 1px solid rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-shield-lock-fill me-1"></i> Thanh Toán Tiện Lợi
                    </span>
                    <h1 class="display-6 fw-bold mb-2 text-white">Thanh Toán Nhanh Chóng & An Toàn Tuyệt Đối</h1>
                    <p class="lead mb-0 text-white-75 max-width-600">
                        Hỗ trợ đa dạng cổng thanh toán: Ví điện tử VNPAY, MoMo, thẻ ATM nội địa & quốc tế. Giao dịch an toàn, bảo mật và xác nhận tức thì.
                    </p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev" style="z-index: 3;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Trước</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next" style="z-index: 3;">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sau</span>
        </button>
    </div>

    <!-- Search Section Panel -->
    <div class="search-panel">
        <h5 class="fw-bold mb-4" style="color: #0f766e;">
            <i class="bi bi-search-heart me-2"></i>Tìm kiếm hành trình đặt vé
        </h5>
        
        <form id="tripSearchForm" method="GET" action="<?= url('/trips/search') ?>" class="row g-3 align-items-center">
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
                    <input name="date" type="date" id="dateInput" class="form-control border-2 border-light-subtle rounded-3">
                    <label for="dateInput" class="text-secondary fw-semibold">Ngày đi</label>
                </div>
            </div>

            <!-- Return Date Picker (Always visible, state controlled via JS) -->
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

    <!-- Advanced Filter and Sorting Dashboard (Hidden initially, will display when results are rendered) -->
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

    <section class="home-trip-section">
        <div class="home-section-header">
            <span class="section-kicker">Chuyến nổi bật</span>
            <div class="home-section-header-inner">
                <div>
                    <h3 class="home-section-title">Các chuyến đi sắp khởi hành</h3>
                    <p class="home-section-subtitle">Những hành trình đang mở bán sắp tới được xếp nhóm trực quan, đặt mua nhanh chóng.</p>
                </div>
                <a href="<?= url('/trips/schedule') ?>" class="home-view-all-btn">Xem lịch trình <i class="bi bi-arrow-right-short"></i></a>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredTrips)): ?>
                <?php
                // Group featured trips by route
                $groupedFeatured = [];
                foreach ($featuredTrips as $t) {
                    $key = (string)($t['from'] ?? '') . '|' . (string)($t['to'] ?? '');
                    if (!isset($groupedFeatured[$key])) {
                        $groupedFeatured[$key] = [
                            'from' => (string)($t['from'] ?? ''),
                            'to' => (string)($t['to'] ?? ''),
                            'duration_minutes' => (int)($t['duration_minutes'] ?? 0),
                            'distance_km' => (int)($t['distance_km'] ?? 0),
                            'minPrice' => (float)($t['price'] ?? 0),
                            'trips' => []
                        ];
                    }
                    $groupedFeatured[$key]['trips'][] = $t;
                    if ((float)$t['price'] < $groupedFeatured[$key]['minPrice']) {
                        $groupedFeatured[$key]['minPrice'] = (float)$t['price'];
                    }
                }
                
                // Sort by departure time within groups
                foreach ($groupedFeatured as &$group) {
                    usort($group['trips'], function($a, $b) {
                        return strtotime($a['departure_time'] ?? 'now') - strtotime($b['departure_time'] ?? 'now');
                    });
                }
                unset($group);

                // Show only first 3 route groups to keep home page clean
                $displayGroups = array_slice($groupedFeatured, 0, 3);
                ?>

                <?php foreach ($displayGroups as $group): ?>
                    <?php
                    $h = floor($group['duration_minutes'] / 60);
                    $m = $group['duration_minutes'] % 60;
                    $durationText = ($h > 0 ? $h . 'h' : '') . ($m > 0 ? $m . 'p' : ($h == 0 ? '0p' : ''));
                    ?>
                    <div class="col-12 route-group-card animate__animated animate__fadeIn">
                        <div class="route-header d-flex flex-wrap justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="route-icon-box">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold route-title"><?= e($group['from']) ?> → <?= e($group['to']) ?></h5>
                                    <div class="route-info-pills">
                                        <span class="info-pill"><i class="bi bi-clock me-1"></i><?= e($durationText) ?></span>
                                        <span class="info-pill"><i class="bi bi-signpost-split me-1"></i><?= e($group['distance_km']) ?> km</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-md-end mt-2 mt-md-0">
                                <div class="price-from-label">Giá chỉ từ</div>
                                <div class="route-price-tag"><?= e(number_format($group['minPrice'], 0, ',', '.')) ?>đ</div>
                            </div>
                        </div>
                        <div class="timetable-wrapper">
                            <table class="table timetable-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Giờ xuất phát</th>
                                        <th>Dòng xe</th>
                                        <th>Giá vé</th>
                                        <th>Chỗ trống</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($group['trips'], 0, 5) as $trip): ?>
                                        <?php $busType = getBusTypeInfo($trip['bus_name'] ?? ''); ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold fs-5 color-teal" style="color: #0f766e;"><?= e(date('H:i', strtotime($trip['departure_time']))) ?></div>
                                                <div class="text-secondary small"><?= e(date('d/m/Y', strtotime($trip['departure_time']))) ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi <?= e($busType['icon']) ?> text-teal"></i>
                                                    <div>
                                                        <div class="fw-bold"><?= e($trip['bus_name'] ?? 'LobiBus') ?></div>
                                                        <span class="badge <?= e($busType['badge']) ?>"><?= e($busType['label']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark fs-5"><?= e(number_format((float) $trip['price'], 0, ',', '.')) ?>đ</div>
                                            </td>
                                            <td>
                                                <div class="text-secondary">
                                                    Còn <span class="fw-bold text-success"><?= e((string) $trip['available_seats']) ?></span> chỗ
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= url('/trips/schedule?from=' . urlencode($trip['from'] ?? '') . '&to=' . urlencode($trip['to'] ?? '') . '&date=' . date('Y-m-d', strtotime($trip['departure_time']))) ?>" class="btn btn-outline-teal rounded-pill px-4 btn-sm fw-bold">
                                                    Chọn chuyến
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="home-trip-empty">Hiện chưa có chuyến đi nổi bật nào.</div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Featured Promotions Section -->
    <section class="home-promo-section">
        <div class="home-section-header">
            <span class="section-kicker" style="background: #ffebe6; color: #ea580c;">Khuyến mãi hot</span>
            <div class="home-section-header-inner">
                <div>
                    <h3 class="home-section-title">Ưu đãi và Khuyến mãi nổi bật</h3>
                    <p class="home-section-subtitle">Săn mã giảm giá, cơ hội hoàn tiền cực khủng và nhiều voucher hấp dẫn khi đặt vé xe khách LobiBus trực tuyến.</p>
                </div>
                <a href="<?= url('/news') ?>" class="home-view-all-btn" style="color: #ea580c; border-color: #ffccbc; background: #ffffff;">
                    Xem tất cả khuyến mãi <i class="bi bi-arrow-right-short" style="color: #ea580c;"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredPromotions)): ?>
                <?php foreach ($featuredPromotions as $promo): ?>
                    <div class="col-12 col-md-6">
                        <article class="home-promo-card">
                            <div class="home-promo-media">
                                <span class="home-promo-badge">Khuyến mãi</span>
                                <img src="<?= asset(e($promo['image'])) ?>" alt="<?= e($promo['title']) ?>" loading="lazy">
                            </div>
                            <div class="home-promo-content">
                                <span class="promo-code-badge"><i class="bi bi-tag-fill me-1"></i>Mã KM: <?= e($promo['code']) ?></span>
                                <h4 class="home-promo-title mt-2">
                                    <a href="<?= url('/news/detail?id=' . $promo['id']) ?>">
                                        <?= e($promo['title']) ?>
                                    </a>
                                </h4>
                                <p class="home-promo-summary"><?= e($promo['summary']) ?></p>
                                <a href="<?= url('/news/detail?id=' . $promo['id']) ?>" class="home-promo-link">
                                    Nhận ưu đãi ngay <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="home-trip-empty">Hiện chưa có chương trình khuyến mãi nào mở bán.</div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="home-news-section">
        <div class="home-section-header">
            <span class="section-kicker">Tin nổi bật</span>
            <div class="home-section-header-inner">
                <div>
                    <h3 class="home-section-title">Xem nhanh tin tức mới nhất</h3>
                    <p class="home-section-subtitle">Các chương trình khuyến mãi, cẩm nang du lịch và thông báo quan trọng của LobiBus.</p>
                </div>
                <a href="<?= url('/news') ?>" class="home-view-all-btn">Xem tất cả tin tức <i class="bi bi-arrow-right-short"></i></a>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($featuredNews, 0, 3) as $news): ?>
                <div class="col-12 col-md-4">
                    <article class="home-news-card">
                        <div class="home-news-media">
                            <span class="home-news-badge">Tin tức</span>
                            <img src="<?= asset(e($news['image'])) ?>" alt="<?= e($news['title']) ?>" loading="lazy">
                        </div>
                        <div class="home-news-content">
                            <div class="home-news-meta">
                                <i class="bi bi-calendar3"></i>
                                <span><?= e($news['date']) ?></span>
                            </div>
                            <h4 class="home-news-title">
                                <a href="<?= url('/news/detail?id=' . $news['id']) ?>">
                                    <?= e($news['title']) ?>
                                </a>
                            </h4>
                            <p class="home-news-summary"><?= e($news['summary']) ?></p>
                            <a href="<?= url('/news/detail?id=' . $news['id']) ?>" class="home-news-link">
                                Đọc chi tiết <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- LobiBus by the Numbers Section -->
    <section class="home-numbers-section mt-5 py-5 px-4 text-white text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 50%, #042f2e 100%); border-radius: 24px; box-shadow: 0 12px 36px rgba(15, 118, 110, 0.15);">
        <!-- Background graphics -->
        <div class="position-absolute" style="top: -50%; right: -20%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(45, 212, 191, 0.15) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>
        <div class="position-absolute" style="bottom: -50%; left: -20%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(45, 212, 191, 0.15) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>

        <div class="row align-items-center g-4 position-relative z-3">
            <div class="col-lg-4 text-lg-start mb-4 mb-lg-0">
                <span class="badge bg-teal-subtle text-success-light mb-2 px-3 py-2 fs-7 fw-bold" style="background-color: rgba(255, 255, 255, 0.15); color: #2dd4bf; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="bi bi-rocket-takeoff-fill me-1"></i> Hành Trình Phát Triển
                </span>
                <h3 class="fw-bold mb-2 text-white" style="font-size: 2.1rem;">LobiBus Bằng Những Con Số</h3>
                <p class="mb-0 text-white-75">
                    Sự tin tưởng của quý khách là động lực to lớn giúp chúng tôi không ngừng cải tiến và mở rộng quy mô phục vụ trên mọi miền Tổ quốc.
                </p>
            </div>
            
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <div class="number-item p-3">
                            <div class="number-icon-wrapper mb-2">
                                <i class="bi bi-emoji-smile-fill text-teal-light fs-2"></i>
                            </div>
                            <div class="display-5 fw-extrabold text-white mb-1">98%</div>
                            <small class="text-white-50">Khách Hàng Hài Lòng</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="number-item p-3">
                            <div class="number-icon-wrapper mb-2">
                                <i class="bi bi-people-fill text-teal-light fs-2"></i>
                            </div>
                            <div class="display-5 fw-extrabold text-white mb-1">1M+</div>
                            <small class="text-white-50">Lượt Khách Phục Vụ</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="number-item p-3">
                            <div class="number-icon-wrapper mb-2">
                                <i class="bi bi-bus-front-fill text-teal-light fs-2"></i>
                            </div>
                            <div class="display-5 fw-extrabold text-white mb-1">50+</div>
                            <small class="text-white-50">Xe Limousine Đời Mới</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="number-item p-3">
                            <div class="number-icon-wrapper mb-2">
                                <i class="bi bi-patch-check-fill text-teal-light fs-2"></i>
                            </div>
                            <div class="display-5 fw-extrabold text-white mb-1">5+</div>
                            <small class="text-white-50">Năm Hoạt Động</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Reviews Section -->
    <section class="home-reviews-section mt-5">
        <div class="home-section-header">
            <span class="section-kicker">Đánh giá thực tế</span>
            <div class="home-section-header-inner">
                <div>
                    <h3 class="home-section-title">Khách hàng nói gì về LobiBus?</h3>
                    <p class="home-section-subtitle">Hàng ngàn hành khách đã trải nghiệm dịch vụ của chúng tôi và chia sẻ những cảm nhận tích cực.</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Review 1 -->
            <div class="col-12 col-md-4">
                <div class="review-card p-4 h-100 position-relative d-flex flex-column">
                    <div class="quote-icon position-absolute" style="top: 20px; right: 25px; opacity: 0.15; font-size: 2.5rem; color: #0f766e;">
                        <i class="bi bi-quote"></i>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-wrapper">
                            <span class="avatar-initials bg-teal-subtle text-teal">TH</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Trần Minh Hoàng</h5>
                            <small class="text-secondary">Doanh nhân • Tuyến TP.HCM - Đà Lạt</small>
                        </div>
                    </div>
                    <div class="rating-stars mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <p class="review-text text-secondary mb-0 flex-grow-1" style="font-size: 0.95rem; line-height: 1.6; font-style: italic;">
                        "Tôi cực kỳ hài lòng với dịch vụ xe VIP Limousine của LobiBus. Xe chạy rất êm, ghế massage cực thoải mái và đặc biệt là đúng giờ, không bắt khách dọc đường. Chắc chắn tôi sẽ tiếp tục ủng hộ!"
                    </p>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="col-12 col-md-4">
                <div class="review-card p-4 h-100 position-relative d-flex flex-column">
                    <div class="quote-icon position-absolute" style="top: 20px; right: 25px; opacity: 0.15; font-size: 2.5rem; color: #0f766e;">
                        <i class="bi bi-quote"></i>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-wrapper">
                            <span class="avatar-initials bg-teal-subtle text-teal">MA</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Nguyễn Mai Anh</h5>
                            <small class="text-secondary">Sinh viên • Tuyến Hà Nội - Sa Pa</small>
                        </div>
                    </div>
                    <div class="rating-stars mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <p class="review-text text-secondary mb-0 flex-grow-1" style="font-size: 0.95rem; line-height: 1.6; font-style: italic;">
                        "Hệ thống đặt vé trực tuyến rất dễ dùng, đặc biệt lại còn được giảm giá sinh viên cực hời. Nhân viên tổng đài và phụ xe lịch thiệp, chu đáo hỗ trợ tôi mang vác hành lý cẩn thận."
                    </p>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="col-12 col-md-4">
                <div class="review-card p-4 h-100 position-relative d-flex flex-column">
                    <div class="quote-icon position-absolute" style="top: 20px; right: 25px; opacity: 0.15; font-size: 2.5rem; color: #0f766e;">
                        <i class="bi bi-quote"></i>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-wrapper">
                            <span class="avatar-initials bg-teal-subtle text-teal">DV</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">David Watson</h5>
                            <small class="text-secondary">Khách du lịch • Tuyến Đà Nẵng - Huế</small>
                        </div>
                    </div>
                    <div class="rating-stars mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-half text-warning"></i>
                    </div>
                    <p class="review-text text-secondary mb-0 flex-grow-1" style="font-size: 0.95rem; line-height: 1.6; font-style: italic;">
                        "Great experience booking tickets through LobiBus! The bus was modern and clean, wifi was fast, and the driver was friendly. Highly recommend LobiBus for everyone traveling in Vietnam!"
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
