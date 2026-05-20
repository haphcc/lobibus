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
    <link rel="stylesheet" href="<?= asset('css/index.css') ?>?v=member1-nav-click-fix">
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
