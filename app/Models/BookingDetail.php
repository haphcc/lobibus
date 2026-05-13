<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class BookingDetail extends Model
{
    public function createMany(int $bookingId, array $seats): bool { return false; }
}
