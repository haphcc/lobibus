<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Seat;

final class SeatApiController extends Controller
{
    public function getByTrip(): void
    {
        $tripId = (int) ($_GET['trip_id'] ?? 0);
        if ($tripId <= 0) {
            $this->json(['message' => 'Thiếu mã chuyến xe.', 'data' => []], 422);
            return;
        }

        $rawSeats = (new Seat())->getSeatStatusByTrip($tripId);
        $firstSeat = $rawSeats[0] ?? [];
        $seats = array_map(static function (array $seat): array {
            return [
                'seat_id' => (int) $seat['seat_id'],
                'seat_number' => (string) $seat['seat_number'],
                'seat_type' => (string) $seat['seat_type'],
                'price' => (float) $seat['price'],
                'status' => (string) $seat['status'],
            ];
        }, $rawSeats);

        $this->json([
            'data' => $seats,
            'meta' => [
                'bus_type' => (string) ($firstSeat['bus_type'] ?? ''),
                'total_seats' => (int) ($firstSeat['total_seats'] ?? count($seats)),
            ],
        ]);
    }
}
