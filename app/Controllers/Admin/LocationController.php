<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class LocationController extends Controller
{
    public function index(): void { $this->view('admin.locations.index', ['title' => 'Quản lý địa điểm'], 'admin'); }
    public function create(): void { $this->view('admin.locations.create', ['title' => 'Thêm địa điểm'], 'admin'); }
    public function store(): void { header('Location: /admin'); }
    public function edit(): void { $this->view('admin.locations.edit', ['title' => 'Sửa địa điểm'], 'admin'); }
    public function update(): void { header('Location: /admin'); }
    public function delete(): void { header('Location: /admin'); }
}
