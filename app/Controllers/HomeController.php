<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Location;

final class HomeController extends Controller
{
    public function index(): void
    {
        $location = new Location();
        $locations = $location->all();
        $this->view('home.index', [
            'title' => 'Trang chủ',
            'locations' => $locations
        ]);
    }
}
