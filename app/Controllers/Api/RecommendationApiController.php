<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Services\RecommendationService;

final class RecommendationApiController extends Controller
{
    public function suggest(): void
    {
        $this->json(['data' => (new RecommendationService())->suggest($_GET)]);
    }
}
