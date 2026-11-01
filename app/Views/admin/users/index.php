<div class="admin-page-header">
    <h1>Người dùng</h1>
    <a class="btn btn-primary" href="<?= url('/admin/users/create') ?>">Thêm người dùng</a>
</div>
<?php require dirname(__DIR__) . '/partials/messages.php'; ?>
<div class="admin-card">
    <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Họ tên</th><th>Email</th><th>Số điện thoại</th><th>Vai trò</th><th>Trạng thái</th><th class="text-end">Thao tác</th></tr></thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= e($user['id']) ?></td>
                <td><?= e($user['name']) ?></td>
                <td>
                    <?= e($user['email']) ?>
                    <?php if (!empty($user['is_google'])): ?>
                        <span class="badge rounded-pill text-bg-light border text-secondary ms-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem; font-weight: 600; padding: 0.2rem 0.4rem;">
                            <svg viewBox="0 0 24 24" width="10" height="10" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </span>
                    <?php endif; ?>
                </td>
                <td><?= e($user['phone']) ?></td>
                <td><?= e(admin_label($user['role_name'])) ?></td>
                <td><span class="badge text-bg-secondary"><?= e(admin_label($user['status'])) ?></span></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="<?= url('/admin/users/edit?id=' . $user['id']) ?>">Sửa</a>
                    <?php if (($user['status'] ?? '') === 'locked'): ?>
                    <form class="d-inline" method="post" action="<?= url('/admin/users/unlock') ?>" data-confirm="Mở khóa người dùng này?">
                        <input type="hidden" name="id" value="<?= e($user['id']) ?>">
                        <button class="btn btn-sm btn-outline-primary" type="submit">Mở khóa</button>
                    </form>
                    <?php else: ?>
                    <form class="d-inline" method="post" action="<?= url('/admin/users/lock') ?>" data-confirm="Khóa người dùng này?">
                        <input type="hidden" name="id" value="<?= e($user['id']) ?>">
                        <button class="btn btn-sm btn-outline-warning" type="submit">Khóa</button>
                    </form>
                    <?php endif; ?>
                    <form class="d-inline" method="post" action="<?= url('/admin/users/delete') ?>" data-confirm="Xóa người dùng này? Chỉ xóa được khi chưa có dữ liệu liên quan.">
                        <input type="hidden" name="id" value="<?= e($user['id']) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
