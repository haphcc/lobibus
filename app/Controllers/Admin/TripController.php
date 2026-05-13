<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class TripController extends Controller
{
    public function index(): void { $this->view('admin.trips.index', ['title' => 'Quản lý chuyến'], 'admin'); }
    public function create(): void { $this->view('admin.trips.create', ['title' => 'Thêm chuyến'], 'admin'); }
    public function store(): void { header('Location: /admin'); }
    public function edit(): void { $this->view('admin.trips.edit', ['title' => 'Sửa chuyến'], 'admin'); }
    public function update(): void { header('Location: /admin'); }
    public function delete(): void { header('Location: /admin'); }
}
