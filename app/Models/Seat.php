<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Seat extends Model
{
    public function getByBus(int $busId): array
    {
        return $this->demoSeats(32);
    }

    public function getByTrip(int $tripId): array
    {
        return $this->demoSeats(32);
    }

    private function demoSeats(int $total): array
    {
        $seats = [];
        foreach (range(1, $total) as $number) {
            $seats[] = [
                'id' => $number,
                'seat_number' => 'A' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'seat_type' => 'standard',
                'status' => $number % 7 === 0 ? 'booked' : 'available',
            ];
        }

        return $seats;
    }
}
