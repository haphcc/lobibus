<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Statistic;

final class DashboardController extends AdminController
{
    public function index(): void
    {
        $statistic = new Statistic();

        $this->view('admin.dashboard.index', [
            'title' => 'Admin Dashboard',
            'summary' => $statistic->dashboardSummary(),
            'revenueByDay' => $statistic->revenueByDay(),
            'bookingStatusBreakdown' => $statistic->bookingStatusBreakdown(),
            'paymentMethodBreakdown' => $statistic->paymentMethodBreakdown(),
            'tripStatusBreakdown' => $statistic->tripStatusBreakdown(),
            'usersByRole' => $statistic->usersByRole(),
            'topRoutes' => $statistic->topRoutes(),
            'upcomingTrips' => $statistic->upcomingTrips(),
            'recentBookings' => $statistic->recentBookings(),
        ], 'admin');
    }
}
