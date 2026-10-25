<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Họ tên</label>
        <input class="form-control" name="name" value="<?= e($user['name'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Số điện thoại</label>
        <input class="form-control" name="phone" value="<?= e($user['phone'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Mật khẩu <?= empty($requirePassword) ? '(để trống nếu không đổi)' : '' ?></label>
        <input class="form-control" type="password" name="password" <?= !empty($requirePassword) ? 'required' : '' ?>>
    </div>
    <div class="col-md-6">
        <label class="form-label">Vai trò</label>
        <select class="form-select" name="role_id">
            <?php foreach ($roles as $role): ?>
                <option value="<?= e($role['id']) ?>" <?= (int) ($user['role_id'] ?? 2) === (int) $role['id'] ? 'selected' : '' ?>><?= e(admin_label($role['name'])) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Trạng thái</label>
        <select class="form-select" name="status">
            <?php foreach (['active', 'locked'] as $status): ?>
                <option value="<?= e($status) ?>" <?= ($user['status'] ?? 'active') === $status ? 'selected' : '' ?>><?= e(admin_label($status)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
