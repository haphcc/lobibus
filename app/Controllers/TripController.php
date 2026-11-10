<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Location;

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
        $this->view('trips.detail', ['title' => 'Chi tiết chuyến']);
    }
}
