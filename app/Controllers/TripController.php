<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Location;
use App\Models\Trip;

final class TripController extends Controller
{
    public function search(): void
    {
        $locations = (new Location())->all();
        $this->view('trips.search', [
            'title' => 'Đặt chuyến',
            'locations' => $locations,
        ]);
    }

    public function schedule(): void
    {
        $locations = (new Location())->all();

        $this->view('trips.schedule', [
            'title' => 'Lịch trình chuyến đi',
            'locations' => $locations,
        ]);
    }

    public function detail(): void
    {
        $tripId = (int) ($_GET['id'] ?? $_GET['trip_id'] ?? 0);
        $trip = $tripId > 0 ? (new Trip())->findWithDetails($tripId) : null;

        $this->view('trips.detail', [
            'title' => 'Chi tiết chuyến',
            'trip' => $trip,
        ]);
    }
}
