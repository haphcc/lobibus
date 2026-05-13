<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Recommendation extends Model
{
    public function suggestTrips(array $context = []): array
    {
        return [
            ['route' => 'Hà Nội -> Hải Phòng', 'price' => 150000],
            ['route' => 'Hà Nội -> Ninh Bình', 'price' => 180000],
        ];
    }
}
