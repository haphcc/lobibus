<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Statistic;

final class DashboardController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.dashboard.index', [
            'title' => 'Admin Dashboard',
            'summary' => (new Statistic())->dashboardSummary(),
        ], 'admin');
    }
}
