<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin.dashboard.index', ['title' => 'Admin Dashboard'], 'admin');
    }
}
