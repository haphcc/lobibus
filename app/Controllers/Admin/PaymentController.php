<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class PaymentController extends Controller
{
    public function index(): void { $this->view('admin.payments.index', ['title' => 'Quản lý thanh toán'], 'admin'); }
    public function updateStatus(): void { header('Location: /admin'); }
}
