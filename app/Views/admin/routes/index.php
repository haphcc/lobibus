<div class="admin-page-header">
    <h1>Tuyến xe</h1>
    <a class="btn btn-primary" href="<?= url('/admin/routes/create') ?>">Thêm tuyến</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="admin-card">
    <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Điểm đi</th><th>Điểm đến</th><th>Quãng đường</th><th>Thời gian</th><th>Trạng thái</th><th class="text-end">Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($routes as $route): ?>
            <tr>
                <td><?= e($route['id']) ?></td>
                <td><?= e($route['from_name']) ?></td>
                <td><?= e($route['to_name']) ?></td>
                <td><?= e($route['distance_km']) ?> km</td>
                <td><?= e($route['duration_minutes']) ?> phút</td>
                <td><span class="badge text-bg-secondary"><?= e(admin_label($route['status'])) ?></span></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/routes/edit?id=' . $route['id']) ?>">Sửa</a>
                    <form class="d-inline" method="post" action="<?= url('/admin/routes/delete') ?>" data-confirm="Xóa tuyến này?">
                        <input type="hidden" name="id" value="<?= e($route['id']) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
