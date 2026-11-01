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
        <?php if (!empty($user['is_google'])): ?>
            <div class="input-group">
                <input class="form-control" type="text" value="Đăng ký bằng Google (Không dùng mật khẩu)" disabled>
            </div>
            <div class="form-text text-success mt-1 d-flex align-items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-info-circle-fill text-success" viewBox="0 0 16 16">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </svg>
                <span>Tài khoản liên kết Google. Không được đổi mật khẩu.</span>
            </div>
        <?php else: ?>
            <div class="input-group">
                <input class="form-control" type="password" id="adminPasswordInput" name="password" <?= !empty($requirePassword) ? 'required' : '' ?>>
                <button class="btn btn-outline-secondary" type="button" id="toggleAdminPasswordBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16" id="eyeIcon">
                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('adminPasswordInput');
    const toggleBtn = document.getElementById('toggleAdminPasswordBtn');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            
            if (isPassword) {
                // Change eye to eye-slash icon SVG content
                eyeIcon.innerHTML = `<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a8.8 8.8 0 0 0-2.79.444l.751.751c.643-.13 1.343-.195 2.039-.195 4.12 0 7.3 3.697 8.243 4.757q.086.13.195.288c-.244.35-.589.845-.989 1.343zM8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/><path d="M10.97 4.97a.75.75 0 0 0-1.07 1.05l-3.99 4.99a.75.75 0 0 0 1.08 1.05z"/><path d="M5.525 7.646a3.5 3.5 0 0 0 4.829 4.829l-.75-.75a2.5 2.5 0 0 1-3.329-3.329zm-2.839-2.31a8.8 8.8 0 0 0-2.242 2.622q-.086.13-.195.288c.335.48.83 1.12 1.465 1.755C3.12 11.332 4.88 12.5 8 12.5c.696 0 1.396-.065 2.04-.195l.751.751C10.06 13.56 8.94 13.5 8 13.5c-4.12 0-7.3-3.697-8.243-4.757a12 12 0 0 1-.195-.288A12 12 0 0 1 2.686 5.336z"/>`;
            } else {
                // Change back to regular eye icon SVG content
                eyeIcon.innerHTML = `<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>`;
            }
        });
    }
});
</script>
