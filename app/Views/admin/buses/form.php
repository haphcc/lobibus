<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Tên xe</label>
        <input class="form-control" name="name" value="<?= e($bus['name'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Biển số</label>
        <input class="form-control" name="license_plate" value="<?= e($bus['license_plate'] ?? '') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Loại xe</label>
        <select class="form-select" name="bus_type">
            <?php foreach (['standard', 'sleeper', 'limousine'] as $type): ?>
                <option value="<?= e($type) ?>" <?= ($bus['bus_type'] ?? 'standard') === $type ? 'selected' : '' ?>><?= e(admin_label($type)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tổng số ghế</label>
        <input class="form-control" type="number" min="1" name="total_seats" value="<?= e($bus['total_seats'] ?? '') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Trạng thái</label>
        <select class="form-select" name="status">
            <?php foreach (['active', 'maintenance', 'inactive'] as $status): ?>
                <option value="<?= e($status) ?>" <?= ($bus['status'] ?? 'active') === $status ? 'selected' : '' ?>><?= e(admin_label($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Đường dẫn ảnh</label>
        <input class="form-control" name="image" value="<?= e($bus['image'] ?? '') ?>">
    </div>
</div>
