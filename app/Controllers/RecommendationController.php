<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class RecommendationController extends Controller
{
    public function index(): void
    {
        $this->view('trips.list', ['title' => 'Gợi ý chuyến xe']);
    }
}
