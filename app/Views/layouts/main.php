<?php $title = $title ?? config('app')['name'] ?? 'LobiBus'; ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | LobiBus</title>
    <link rel="icon" href="<?= asset('images/logo.svg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/index.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/customer.css') ?>">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.APP_BASE_URL = '<?= e(url()) ?>'.replace(/\/$/, '');</script>
<?php foreach (($pageJs ?? []) as $js): ?>
    <script src="<?= asset('js/' . $js) ?>"></script>
<?php endforeach; ?>
</body>
</html>
