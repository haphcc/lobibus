<?php
$departureValue = !empty($trip['departure_time']) ? date('Y-m-d\TH:i', strtotime((string) $trip['departure_time'])) : '';
$arrivalValue = !empty($trip['arrival_time']) ? date('Y-m-d\TH:i', strtotime((string) $trip['arrival_time'])) : '';
?>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Tuyến</label>
        <select class="form-select" name="route_id" required>
            <?php foreach ($routes as $route): ?>
                <option value="<?= e($route['id']) ?>" <?= (int) ($trip['route_id'] ?? 0) === (int) $route['id'] ? 'selected' : '' ?>>
                    <?= e($route['from_name']) ?> -> <?= e($route['to_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Xe</label>
        <select class="form-select" name="bus_id" required>
            <?php foreach ($buses as $bus): ?>
                <option value="<?= e($bus['id']) ?>" <?= (int) ($trip['bus_id'] ?? 0) === (int) $bus['id'] ? 'selected' : '' ?>>
                    <?= e($bus['name']) ?> (<?= e($bus['license_plate']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Giờ khởi hành</label>
        <input class="form-control" type="datetime-local" name="departure_time" value="<?= e($departureValue) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Giờ đến</label>
        <input class="form-control" type="datetime-local" name="arrival_time" value="<?= e($arrivalValue) ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Giá vé</label>
        <input class="form-control" type="number" min="0" name="price" value="<?= e($trip['price'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Trạng thái</label>
        <select class="form-select" name="status">
            <?php foreach (['scheduled', 'running', 'completed', 'cancelled'] as $status): ?>
                <option value="<?= e($status) ?>" <?= ($trip['status'] ?? 'scheduled') === $status ? 'selected' : '' ?>><?= e(admin_label($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
