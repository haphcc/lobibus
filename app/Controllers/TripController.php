<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class TripController extends Controller
{
    public function search(): void
    {
        $this->view('trips.search', ['title' => 'Tìm chuyến']);
    }

    public function detail(): void
    {
        $this->view('trips.detail', ['title' => 'Chi tiết chuyến']);
    }
}
