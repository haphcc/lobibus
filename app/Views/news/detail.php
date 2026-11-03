<?php
$pageCss = ['news.css'];
?>
<article class="news-detail-page">
    <div class="container">
        <!-- Back Button -->
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <a href="<?= url('/news') ?>" class="detail-back-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>
                    Quay lại Tin tức
                </a>
            </div>
        </div>

        <!-- Article Header -->
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <div class="detail-header">
                    <?php
                        $categoryLabel = 'Tin tức';
                        if ($article['category'] === 'featured') {
                            $categoryLabel = 'Tin nổi bật';
                        } elseif ($article['category'] === 'spotlight') {
                            $categoryLabel = 'Tiêu điểm';
                        }
                    ?>
                    <div class="detail-category"><?= e($categoryLabel) ?></div>
                    <h1 class="detail-title"><?= e($article['title']) ?></h1>
                    
                    <div class="detail-meta-bar">
                        <div class="detail-meta-item">
                            <span class="detail-meta-icon">📅</span>
                            <span>Ngày đăng: <?= e($article['date']) ?></span>
                        </div>
                        <div class="detail-meta-item">
                            <span class="detail-meta-icon">👤</span>
                            <span>Tác giả: <strong><?= e($article['author']) ?></strong></span>
                        </div>
                        <div class="detail-meta-item">
                            <span class="detail-meta-icon">⏱️</span>
                            <span>Đọc trong: 3 phút</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary & Image -->
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <!-- Summary block -->
                <div class="detail-summary-box">
                    <?= e($article['summary']) ?>
                </div>

                <!-- Main Image -->
                <div class="detail-image-wrap">
                    <img src="<?= asset(e($article['image'])) ?>" alt="<?= e($article['title']) ?>" class="detail-image img-fluid w-100">
                </div>
            </div>
        </div>

        <!-- Detailed Content -->
        <div class="row mb-5">
            <div class="col-12 col-lg-8 offset-lg-2">
                <div class="detail-content">
                    <?= $article['content'] // Raw HTML is safe as it's hardcoded static controller content ?>
                </div>
            </div>
        </div>
    </div>
</article>

<!-- Related Articles Section -->
<?php if (!empty($related)): ?>
    <section class="related-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h4 class="related-title">Bài viết liên quan</h4>
                </div>
            </div>
            <div class="row g-4 mt-1">
                <?php foreach ($related as $item): 
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
                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <div class="news-card-img-wrap">
                                <span class="category-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                <img src="<?= asset(e($item['image'])) ?>" alt="<?= e($item['title']) ?>" class="news-card-img" loading="lazy">
                            </div>
                            <div class="news-card-body">
                                <div class="news-meta">
                                    <span>📅 <?= e($item['date']) ?></span>
                                </div>
                                <h5 class="news-card-title" style="font-size:1.1rem; height: 3.08rem;">
                                    <a href="<?= url('/news/detail?id=' . $item['id']) ?>" class="text-decoration-none text-dark hover-teal">
                                        <?= e($item['title']) ?>
                                    </a>
                                </h5>
                                <p class="news-card-desc" style="-webkit-line-clamp: 2; font-size: 0.85rem;"><?= e($item['summary']) ?></p>
                                <div class="news-card-footer">
                                    <a href="<?= url('/news/detail?id=' . $item['id']) ?>" class="read-more-btn">Đọc tiếp</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
