<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Review extends Model
{
    public function allByTrip(int $tripId): array { return []; }
}
