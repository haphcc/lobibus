<div class="admin-page-header">
    <h1>Xe</h1>
    <a class="btn btn-primary" href="<?= url('/admin/buses/create') ?>">Thêm xe</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="admin-card">
    <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Tên xe</th><th>Biển số</th><th>Loại xe</th><th>Số ghế</th><th>Trạng thái</th><th class="text-end">Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($buses as $bus): ?>
            <tr>
                <td><?= e($bus['id']) ?></td>
                <td><?= e($bus['name']) ?></td>
                <td><?= e($bus['license_plate']) ?></td>
                <td><?= e(admin_label($bus['bus_type'])) ?></td>
                <td><?= e($bus['total_seats']) ?></td>
                <td><span class="badge text-bg-secondary"><?= e(admin_label($bus['status'])) ?></span></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= url('/admin/seats?bus_id=' . $bus['id']) ?>">Ghế</a>
                    <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/buses/edit?id=' . $bus['id']) ?>">Sửa</a>
                    <form class="d-inline" method="post" action="<?= url('/admin/buses/delete') ?>" data-confirm="Xóa xe này?">
                        <input type="hidden" name="id" value="<?= e($bus['id']) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
