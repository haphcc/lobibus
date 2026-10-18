<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Statistic extends Model
{
    public function dashboardSummary(): array
    {
        return ['users' => 0, 'trips' => 0, 'bookings' => 0, 'revenue' => 0];
    }
}
