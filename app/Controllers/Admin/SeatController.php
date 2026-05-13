<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class SeatController extends Controller
{
    public function index(): void { $this->view('admin.seats.index', ['title' => 'Quản lý ghế'], 'admin'); }
    public function create(): void { $this->view('admin.seats.create', ['title' => 'Thêm ghế'], 'admin'); }
    public function store(): void { header('Location: /admin'); }
}
