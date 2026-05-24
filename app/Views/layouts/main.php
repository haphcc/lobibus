<?php $title = $title ?? config('app')['name'] ?? 'LobiBus'; ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title) ?> | LobiBus</title>
    <link rel="icon" href="<?= asset('images/logo.svg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= asset('css/index.css') ?>?v=zoom-fix-v4">
    <link rel="stylesheet" href="<?= asset('css/customer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/chatbot.css') ?>?v=bubble-v1">
    <?php foreach (($pageCss ?? []) as $css): ?>
        <link rel="stylesheet" href="<?= asset('css/' . $css) ?>">
    <?php endforeach; ?>
</head>
<body>
<?php require __DIR__ . '/navbar.php'; ?>
<main>
    <?= $content ?>
</main>
<?php require __DIR__ . '/footer.php'; ?>

<!-- Chatbot Floating Bubble Component -->
<?php require dirname(__DIR__) . '/chatbot/bubble.php'; ?>

<!-- Map Preview Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white border-0 py-3" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-map-fill me-2"></i>Bản đồ Hành trình & Điểm đón/trả
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Left Panel: Stations Details -->
                    <div class="col-md-4 bg-light p-3 border-end">
                        <h6 class="fw-bold mb-3" style="color: #0f766e;"><i class="bi bi-info-circle-fill me-1"></i>Hành trình chi tiết</h6>
                        <div class="d-flex flex-column gap-3">
                            <div class="station-info-box">
                                <span class="badge bg-success mb-1">Điểm đón</span>
                                <div class="fw-bold text-dark fs-6" id="mapFromStation">-</div>
                                <small class="text-muted d-block mt-1" id="mapFromAddress">-</small>
                            </div>
                            <hr class="my-1">
                            <div class="station-info-box">
                                <span class="badge bg-danger mb-1">Điểm trả</span>
                                <div class="fw-bold text-dark fs-6" id="mapToStation">-</div>
                                <small class="text-muted d-block mt-1" id="mapToAddress">-</small>
                            </div>
                        </div>
                    </div>
                    <!-- Right Panel: Map Container -->
                    <div class="col-md-8 position-relative">
                        <div id="leafletMap" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.APP_BASE_URL = '<?= e(url()) ?>'.replace(/\/$/, '');
window.CSRF_TOKEN = '<?= e(csrf_token()) ?>';
</script>
<?php foreach (($pageJs ?? []) as $js): ?>
    <script src="<?= asset('js/' . $js) ?>"></script>
<?php endforeach; ?>
<script src="<?= asset('js/chatbot.js') ?>?v=bubble-v1"></script>
</body>
</html>
