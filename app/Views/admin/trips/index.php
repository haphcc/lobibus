<?php
$filters = $filters ?? ['q' => ''];
$pagination = $pagination ?? ['page' => 1, 'per_page' => 25, 'total' => count($trips ?? []), 'total_pages' => 1];
$query = trim((string) ($filters['q'] ?? ''));
$page = (int) ($pagination['page'] ?? 1);
$totalPages = (int) ($pagination['total_pages'] ?? 1);
$totalTrips = (int) ($pagination['total'] ?? 0);
$perPage = (int) ($pagination['per_page'] ?? 25);
$fromItem = $totalTrips === 0 ? 0 : (($page - 1) * $perPage) + 1;
$toItem = min($totalTrips, $page * $perPage);
$pageUrl = static function (int $targetPage) use ($query): string {
    $params = ['page' => $targetPage];
    if ($query !== '') {
        $params['q'] = $query;
    }

    return url('/admin/trips?' . http_build_query($params));
};
?>
<div class="admin-page-header">
    <h1>Chuyến xe</h1>
    <a class="btn btn-primary" href="<?= url('/admin/trips/create') ?>">Thêm chuyến</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>

<form class="admin-card admin-list-tools" method="get" action="<?= url('/admin/trips') ?>">
    <div>
        <label class="form-label" for="tripSearch">Tìm chuyến</label>
        <input
            class="form-control"
            id="tripSearch"
            name="q"
            type="search"
            inputmode="search"
            value="<?= e($query) ?>"
            placeholder="Nhập ID chuyến, tên tuyến hoặc tên xe"
        >
    </div>
    <div class="admin-list-actions">
        <button class="btn btn-primary" type="submit">Tìm</button>
        <?php if ($query !== ''): ?>
            <a class="btn btn-outline-secondary" href="<?= url('/admin/trips') ?>">Xóa lọc</a>
        <?php endif; ?>
    </div>
</form>

<div class="admin-card">
    <div class="admin-table-summary">
        Hiển thị <?= number_format($fromItem) ?>-<?= number_format($toItem) ?> / <?= number_format($totalTrips) ?> chuyến
    </div>
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tuyến</th>
                <th>Xe</th>
                <th>Giờ đi</th>
                <th>Giờ đến</th>
                <th>Giá vé</th>
                <th>Trạng thái</th>
                <th class="text-end">Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($trips)): ?>
            <tr>
                <td colspan="8" class="text-center text-muted py-4">Không tìm thấy chuyến phù hợp.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($trips as $trip): ?>
            <tr>
                <td data-label="ID"><?= e($trip['id']) ?></td>
                <td data-label="Tuyến"><?= e($trip['from_name']) ?> -> <?= e($trip['to_name']) ?></td>
                <td data-label="Xe"><?= e($trip['bus_name']) ?></td>
                <td data-label="Giờ đi"><?= e($trip['departure_time']) ?></td>
                <td data-label="Giờ đến"><?= e($trip['arrival_time']) ?></td>
                <td data-label="Giá vé"><?= number_format((float) $trip['price']) ?> VND</td>
                <td data-label="Trạng thái"><span class="badge text-bg-secondary"><?= e(admin_label($trip['status'])) ?></span></td>
                <td data-label="Thao tác" class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/trips/edit?id=' . $trip['id']) ?>">Sửa</a>
                    <form class="d-inline" method="post" action="<?= url('/admin/trips/delete') ?>" data-confirm="Xóa chuyến này?">
                        <input type="hidden" name="id" value="<?= e($trip['id']) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
        <nav class="admin-pagination" aria-label="Phân trang chuyến xe">
            <a class="btn btn-sm btn-outline-secondary <?= $page <= 1 ? 'disabled' : '' ?>" href="<?= $page <= 1 ? '#' : $pageUrl($page - 1) ?>">Trước</a>
            <span>Trang <?= number_format($page) ?> / <?= number_format($totalPages) ?></span>
            <a class="btn btn-sm btn-outline-secondary <?= $page >= $totalPages ? 'disabled' : '' ?>" href="<?= $page >= $totalPages ? '#' : $pageUrl($page + 1) ?>">Sau</a>
        </nav>
    <?php endif; ?>
</div>
