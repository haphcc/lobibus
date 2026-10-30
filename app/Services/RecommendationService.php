<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Recommendation;

final class RecommendationService
{
    public function suggest(array $context = []): array
    {
        return (new Recommendation())->suggestTrips($context);
    }
}
