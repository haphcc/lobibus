<div class="admin-page-header">
    <h1>Địa điểm</h1>
    <a class="btn btn-primary" href="<?= url('/admin/locations/create') ?>">Thêm địa điểm</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="admin-card">
    <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Tên địa điểm</th><th>Tỉnh/Thành</th><th>Địa chỉ</th><th>Tọa độ</th><th class="text-end">Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($locations as $location): ?>
            <tr>
                <td><?= e($location['id']) ?></td>
                <td><?= e($location['name']) ?></td>
                <td><?= e($location['province']) ?></td>
                <td><?= e($location['address']) ?></td>
                <td><?= e($location['latitude']) ?>, <?= e($location['longitude']) ?></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/locations/edit?id=' . $location['id']) ?>">Sửa</a>
                    <form class="d-inline" method="post" action="<?= url('/admin/locations/delete') ?>" data-confirm="Xóa địa điểm này?">
                        <input type="hidden" name="id" value="<?= e($location['id']) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
