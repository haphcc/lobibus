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
                <td><?= e($user['email']) ?></td>
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
