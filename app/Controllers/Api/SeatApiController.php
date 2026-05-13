<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Seat;

final class SeatApiController extends Controller
{
    public function getByTrip(): void
    {
        $tripId = (int) ($_GET['trip_id'] ?? 0);
        $this->json(['data' => (new Seat())->getByTrip($tripId)]);
    }
}
