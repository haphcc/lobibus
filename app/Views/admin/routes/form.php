<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Điểm đi</label>
        <select class="form-select" name="from_location_id" required>
            <?php foreach ($locations as $location): ?>
                <option value="<?= e($location['id']) ?>" <?= (int) ($route['from_location_id'] ?? 0) === (int) $location['id'] ? 'selected' : '' ?>><?= e($location['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Điểm đến</label>
        <select class="form-select" name="to_location_id" required>
            <?php foreach ($locations as $location): ?>
                <option value="<?= e($location['id']) ?>" <?= (int) ($route['to_location_id'] ?? 0) === (int) $location['id'] ? 'selected' : '' ?>><?= e($location['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Quãng đường (km)</label>
        <input class="form-control" name="distance_km" value="<?= e($route['distance_km'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Thời gian dự kiến (phút)</label>
        <input class="form-control" name="duration_minutes" value="<?= e($route['duration_minutes'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Trạng thái</label>
        <select class="form-select" name="status">
            <?php foreach (['active', 'inactive'] as $status): ?>
                <option value="<?= e($status) ?>" <?= ($route['status'] ?? 'active') === $status ? 'selected' : '' ?>><?= e(admin_label($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
