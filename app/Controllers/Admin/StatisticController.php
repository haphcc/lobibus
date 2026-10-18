<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class StatisticController extends Controller
{
    public function index(): void { $this->view('admin.statistics.index', ['title' => 'Thống kê'], 'admin'); }
}
