<div class="admin-page-header">
    <h1>Thêm ghế</h1>
    <a class="btn btn-outline-secondary" href="<?= url('/admin/seats?bus_id=' . (int) $busId) ?>">Quay lại</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<form class="admin-card admin-form" method="post" action="<?= url('/admin/seats/store') ?>">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Xe</label>
            <select class="form-select" name="bus_id" required>
                <?php foreach ($buses as $bus): ?>
                    <option value="<?= e($bus['id']) ?>" <?= (int) $busId === (int) $bus['id'] ? 'selected' : '' ?>>
                        <?= e($bus['name']) ?> (<?= e($bus['license_plate']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Số ghế</label>
            <input class="form-control" name="seat_number" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Loại ghế</label>
            <select class="form-select" name="seat_type">
                <?php foreach (['standard', 'sleeper', 'vip'] as $type): ?>
                    <option value="<?= e($type) ?>"><?= e(admin_label($type)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button class="btn btn-primary mt-3" type="submit">Tạo mới</button>
</form>
