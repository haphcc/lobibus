<?php
declare(strict_types=1);
namespace App\Services;

final class RecommendationService
{
    public function suggest(array $context = []): array
    {
        return [
            ['route' => 'Hà Nội -> Hải Phòng', 'reason' => 'Tuyến gần, nhiều chuyến trong ngày'],
            ['route' => 'Hà Nội -> Ninh Bình', 'reason' => 'Phù hợp đi cuối tuần'],
        ];
    }
}
