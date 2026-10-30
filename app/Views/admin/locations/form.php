<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Tên địa điểm</label>
        <input class="form-control" name="name" value="<?= e($location['name'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Tỉnh/Thành</label>
        <input class="form-control" name="province" value="<?= e($location['province'] ?? '') ?>">
    </div>
    <div class="col-12">
        <label class="form-label">Địa chỉ</label>
        <input class="form-control" name="address" value="<?= e($location['address'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Vĩ độ</label>
        <input class="form-control" type="number" step="0.0000001" min="-90" max="90" name="latitude" value="<?= e($location['latitude'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Kinh độ</label>
        <input class="form-control" type="number" step="0.0000001" min="-180" max="180" name="longitude" value="<?= e($location['longitude'] ?? '') ?>">
    </div>
</div>
