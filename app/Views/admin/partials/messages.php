<?php require_once __DIR__ . '/labels.php'; ?>
<?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success"><?= e($_GET['success']) ?></div>
<?php endif; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger"><?= e($_GET['error']) ?></div>
<?php endif; ?>
