<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class UserController extends Controller
{
    public function index(): void { $this->view('admin.users.index', ['title' => 'Quản lý người dùng'], 'admin'); }
    public function create(): void { $this->view('admin.users.create', ['title' => 'Thêm người dùng'], 'admin'); }
    public function store(): void { header('Location: /admin'); }
    public function edit(): void { $this->view('admin.users.edit', ['title' => 'Sửa người dùng'], 'admin'); }
    public function update(): void { header('Location: /admin'); }
    public function delete(): void { header('Location: /admin'); }
}
