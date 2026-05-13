<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Trip;

final class TripApiController extends Controller
{
    public function search(): void
    {
        $this->json(['data' => (new Trip())->search($_GET)]);
    }
}
