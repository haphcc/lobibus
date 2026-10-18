<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class BookingController extends Controller
{
    public function index(): void { $this->view('admin.bookings.index', ['title' => 'Quản lý đặt vé'], 'admin'); }
    public function detail(): void { $this->view('admin.bookings.detail', ['title' => 'Chi tiết đặt vé'], 'admin'); }
    public function updateStatus(): void { header('Location: /admin'); }
}
