<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Recommendation;

final class RecommendationApiController extends Controller
{
    public function suggest(): void
    {
        $this->json(['data' => (new Recommendation())->suggestTrips($_GET)]);
    }
}
