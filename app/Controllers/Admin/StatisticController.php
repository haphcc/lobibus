<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Statistic;

final class StatisticController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.statistics.index', [
            'title' => 'Statistics',
            'summary' => (new Statistic())->dashboardSummary(),
        ], 'admin');
    }
}
