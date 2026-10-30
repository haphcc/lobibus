<div class="admin-page-header">
    <h1>Ghế</h1>
    <a class="btn btn-primary" href="<?= url('/admin/seats/create?bus_id=' . (int) ($selectedBus['id'] ?? 0)) ?>">Thêm ghế</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<form class="admin-card mb-3" method="get" action="<?= url('/admin/seats') ?>">
    <label class="form-label">Xe</label>
    <div class="admin-inline-form">
        <select class="form-select" name="bus_id">
            <?php foreach ($buses as $bus): ?>
                <option value="<?= e($bus['id']) ?>" <?= (int) ($selectedBus['id'] ?? 0) === (int) $bus['id'] ? 'selected' : '' ?>>
                    <?= e($bus['name']) ?> (<?= e($bus['license_plate']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-outline-primary" type="submit">Lọc</button>
    </div>
</form>
<div class="admin-card">
    <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Xe</th><th>Số ghế</th><th>Loại ghế</th><th class="text-end">Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($seats as $seat): ?>
            <tr>
                <td><?= e($seat['id']) ?></td>
                <td><?= e($selectedBus['name'] ?? '') ?></td>
                <td><?= e($seat['seat_number']) ?></td>
                <td><?= e(admin_label($seat['seat_type'])) ?></td>
                <td class="text-end">
                    <form class="d-inline" method="post" action="<?= url('/admin/seats/delete') ?>" data-confirm="Xóa ghế này?">
                        <input type="hidden" name="id" value="<?= e($seat['id']) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
