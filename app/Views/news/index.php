<?php
$pageCss = ['news.css'];
?>
<section class="news-page">
    <div class="container">
        <!-- Breadcrumb / Header -->
        <div class="row mb-4">
            <div class="col-12">
                <span class="section-kicker">Tin tức LobiBus</span>
                <h1 class="news-title">Tin tức mới nhất</h1>
                <p class="news-subtitle">Cập nhật những tin tức mới nhất về các chuyến đi, sự kiện, chương trình khuyến mãi và dịch vụ tiện ích từ hãng xe LobiBus.</p>
            </div>
        </div>

        <!-- 1. Hero Layout: Featured & Spotlight Combined -->
        <div class="row g-4 news-hero-section">
            <!-- Left Side: Large Featured Article -->
            <div class="col-12 col-lg-8">
                <?php if (!empty($featured)): 
                    $mainFeatured = reset($featured); // Get the first featured article
                ?>
                    <div class="hero-large-card h-100">
                        <div class="row g-0 h-100">
                            <div class="col-12 col-md-6 order-md-2">
                                <div class="hero-large-img-wrap">
                                    <span class="category-badge badge-featured">Nổi bật</span>
                                    <img src="<?= asset(e($mainFeatured['image'])) ?>" alt="<?= e($mainFeatured['title']) ?>" class="hero-large-img">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 order-md-1">
                                <div class="hero-large-body">
                                    <div class="news-meta">
                                        <span>📅 <?= e($mainFeatured['date']) ?></span>
                                        <span class="meta-divider"></span>
                                        <span>👤 <?= e($mainFeatured['author']) ?></span>
                                    </div>
                                    <h2 class="hero-large-title">
                                        <a href="<?= url('/news/detail?id=' . $mainFeatured['id']) ?>" class="text-decoration-none text-dark hover-teal">
                                            <?= e($mainFeatured['title']) ?>
                                        </a>
                                    </h2>
                                    <p class="hero-large-desc"><?= e($mainFeatured['summary']) ?></p>
                                    <div class="mt-auto">
                                        <a href="<?= url('/news/detail?id=' . $mainFeatured['id']) ?>" class="read-more-btn">Đọc tiếp</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Side: Spotlight list -->
            <div class="col-12 col-lg-4">
                <div class="spotlight-list-card">
                    <div class="spotlight-header">
                        <span class="spotlight-title-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-lightning-charge-fill" viewBox="0 0 16 16">
                                <path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
                            </svg>
                            Tiêu điểm
                        </span>
                        <span class="badge bg-danger rounded-pill text-uppercase" style="font-size:0.65rem;letter-spacing:1px;">Hot</span>
                    </div>
                    <div class="spotlight-body d-flex flex-column gap-3">
                        <?php if (!empty($spotlight)): ?>
                            <?php foreach ($spotlight as $item): ?>
                                <div class="spotlight-item">
                                    <div class="spotlight-item-meta">
                                        <span>📅 <?= e($item['date']) ?></span>
                                    </div>
                                    <a href="<?= url('/news/detail?id=' . $item['id']) ?>" class="spotlight-item-title">
                                        <?= e($item['title']) ?>
                                    </a>
                                    <p class="spotlight-item-desc text-muted mb-0"><?= e($item['summary']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-4 my-0">Chưa có bài viết tiêu điểm nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Interactive Navigation Filters -->
        <div class="row mb-5 text-center">
            <div class="col-12">
                <div class="news-filters" role="group" aria-label="Bộ lọc tin tức">
                    <button type="button" class="filter-btn active" data-filter="all">Tất cả tin tức</button>
                    <button type="button" class="filter-btn" data-filter="featured">Tin tức nổi bật</button>
                    <button type="button" class="filter-btn" data-filter="spotlight">Tiêu điểm LobiBus</button>
                </div>
            </div>
        </div>

        <!-- 3. All News Grid -->
        <div class="row g-4" id="newsGrid">
            <?php if (!empty($all)): ?>
                <?php foreach ($all as $item): 
                    // Map category classes
                    $badgeClass = 'badge-all';
                    $badgeLabel = 'Tin tức';
                    if ($item['category'] === 'featured') {
                        $badgeClass = 'badge-featured';
                        $badgeLabel = 'Nổi bật';
                    } elseif ($item['category'] === 'spotlight') {
                        $badgeClass = 'badge-spotlight';
                        $badgeLabel = 'Tiêu điểm';
                    }
                ?>
                    <div class="col-12 col-md-6 col-lg-4 news-grid-item" data-category="<?= e($item['category']) ?>">
                        <div class="news-card">
                            <div class="news-card-img-wrap">
                                <span class="category-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                <img src="<?= asset(e($item['image'])) ?>" alt="<?= e($item['title']) ?>" class="news-card-img" loading="lazy">
                            </div>
                            <div class="news-card-body">
                                <div class="news-meta">
                                    <span>📅 <?= e($item['date']) ?></span>
                                    <span class="meta-divider"></span>
                                    <span>👤 <?= e($item['author']) ?></span>
                                </div>
                                <h3 class="news-card-title">
                                    <a href="<?= url('/news/detail?id=' . $item['id']) ?>" class="text-decoration-none text-dark">
                                        <?= e($item['title']) ?>
                                    </a>
                                </h3>
                                <p class="news-card-desc"><?= e($item['summary']) ?></p>
                                <div class="news-card-footer">
                                    <a href="<?= url('/news/detail?id=' . $item['id']) ?>" class="read-more-btn">Đọc chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted h5">Không tìm thấy bài viết nào.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Smooth Filtering JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const newsItems = document.querySelectorAll('.news-grid-item');
    const newsGrid = document.getElementById('newsGrid');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            // Apply fade-out transition
            newsGrid.style.opacity = '0.3';
            newsGrid.style.transition = 'opacity 0.2s ease-in-out';

            setTimeout(() => {
                newsItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    if (filterValue === 'all' || itemCategory === filterValue) {
                        item.classList.remove('d-none');
                        // Trigger small animation
                        item.style.animation = 'fadeInUp 0.5s ease forwards';
                    } else {
                        item.classList.add('d-none');
                    }
                });
                
                // Restore opacity
                newsGrid.style.opacity = '1';
            }, 200);
        });
    });
});
</script>

<!-- Simple Keyframe Style for Grid filter animation -->
<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
