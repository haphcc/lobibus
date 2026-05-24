<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Role;
use App\Models\User;

final class UserController extends AdminController
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        $this->view('admin.users.index', [
            'title' => 'Quản lý người dùng',
            'users' => $this->users->allWithRoles(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.users.create', [
            'title' => 'Them nguoi dung',
            'roles' => (new Role())->all(),
        ], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields([
            'name' => 'họ tên',
            'email' => 'Email',
            'password' => 'mật khẩu',
        ])) {
            $this->redirect('/admin/users/create', 'error', $error);
        }

        if (!filter_var($this->postString('email'), FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/users/create', 'error', 'Email không đúng định dạng.');
        }
        if ($error = $this->requireAllowed('status', 'Trạng thái', ['active', 'locked'])) {
            $this->redirect('/admin/users/create', 'error', $error);
        }

        $this->users->create($_POST);
        $this->redirect('/admin/users', 'success', 'Đã thêm người dùng.');
    }

    public function edit(): void
    {
        $user = $this->users->find($this->queryInt('id'));
        if ($user === null) {
            $this->redirect('/admin/users', 'error', 'Không tìm thấy người dùng.');
        }

        $this->view('admin.users.edit', [
            'title' => 'Sua nguoi dung',
            'user' => $user,
            'roles' => (new Role())->all(),
        ], 'admin');
    }

    public function update(): void
    {
        $id = $this->postInt('id');
        $user = $this->users->find($id);
        if ($user === null) {
            $this->redirect('/admin/users', 'error', 'Không tìm thấy người dùng.');
        }

        if ($error = $this->requireFields([
            'name' => 'họ tên',
            'email' => 'Email',
        ])) {
            $this->redirect('/admin/users/edit?id=' . $id, 'error', $error);
        }

        if (!filter_var($this->postString('email'), FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/users/edit?id=' . $id, 'error', 'Email không đúng định dạng.');
        }
        if ($error = $this->requireAllowed('status', 'Trạng thái', ['active', 'locked'])) {
            $this->redirect('/admin/users/edit?id=' . $id, 'error', $error);
        }

        $data = $_POST;
        if (!empty($user['is_google'])) {
            unset($data['password']);
        }

        $this->users->update($id, $data);
        $this->redirect('/admin/users', 'success', 'Đã cập nhật người dùng.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->users->isUsed($id)) {
            $this->redirect('/admin/users', 'error', 'Không thể xóa người dùng đã có dữ liệu đặt vé hoặc đánh giá.');
        }

        $this->users->delete($id);
        $this->redirect('/admin/users', 'success', 'Đã xóa người dùng.');
    }

    public function lock(): void
    {
        $this->users->lock($this->postInt('id'));
        $this->redirect('/admin/users', 'success', 'Đã khóa người dùng.');
    }

    public function unlock(): void
    {
        $this->users->unlock($this->postInt('id'));
        $this->redirect('/admin/users', 'success', 'Đã mở khóa người dùng.');
    }
}
