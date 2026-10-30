<div class="admin-page-header">
    <h1>Chuyến xe</h1>
    <a class="btn btn-primary" href="<?= url('/admin/trips/create') ?>">Thêm chuyến</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="admin-card">
    <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Tuyến</th><th>Xe</th><th>Giờ đi</th><th>Giờ đến</th><th>Giá vé</th><th>Trạng thái</th><th class="text-end">Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($trips as $trip): ?>
            <tr>
                <td><?= e($trip['id']) ?></td>
                <td><?= e($trip['from_name']) ?> -> <?= e($trip['to_name']) ?></td>
                <td><?= e($trip['bus_name']) ?></td>
                <td><?= e($trip['departure_time']) ?></td>
                <td><?= e($trip['arrival_time']) ?></td>
                <td><?= number_format((float) $trip['price']) ?> VND</td>
                <td><span class="badge text-bg-secondary"><?= e(admin_label($trip['status'])) ?></span></td>
                <td class="text-end">
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
</div>
