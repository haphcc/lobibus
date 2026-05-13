<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class BusController extends Controller
{
    public function index(): void { $this->view('admin.buses.index', ['title' => 'Quản lý xe'], 'admin'); }
    public function create(): void { $this->view('admin.buses.create', ['title' => 'Thêm xe'], 'admin'); }
    public function store(): void { header('Location: /admin'); }
    public function edit(): void { $this->view('admin.buses.edit', ['title' => 'Sửa xe'], 'admin'); }
    public function update(): void { header('Location: /admin'); }
    public function delete(): void { header('Location: /admin'); }
}
