<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class RouteController extends Controller
{
    public function index(): void { $this->view('admin.routes.index', ['title' => 'Quản lý tuyến'], 'admin'); }
    public function create(): void { $this->view('admin.routes.create', ['title' => 'Thêm tuyến'], 'admin'); }
    public function store(): void { header('Location: /admin'); }
    public function edit(): void { $this->view('admin.routes.edit', ['title' => 'Sửa tuyến'], 'admin'); }
    public function update(): void { header('Location: /admin'); }
    public function delete(): void { header('Location: /admin'); }
}
