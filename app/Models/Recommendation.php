<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Recommendation extends Model
{
    public function suggestTrips(array $context = []): array
    {
        $suggestions = [];

        foreach ([
            ['cheap', 'Chuyến rẻ nhất'],
            ['early', 'Khởi hành sớm nhất'],
            ['available', 'Còn nhiều ghế nhất'],
            ['popular', 'Phổ biến nhất'],
        ] as [$type, $reason]) {
            foreach ($this->queryTrips($type) as $trip) {
                $trip['reason'] = $reason;
                $suggestions[] = $trip;
            }
        }

        return $suggestions;
    }

    private function queryTrips(string $type): array
    {
        $orderBy = match ($type) {
            'cheap' => 't.price ASC, t.departure_time ASC',
            'early' => 't.departure_time ASC, t.price ASC',
            'available' => 'available_seats DESC, t.departure_time ASC',
            'popular' => 'booking_count DESC, t.departure_time ASC',
            default => 't.departure_time ASC',
        };

        $sql = "
            SELECT
                t.id AS trip_id,
                CONCAT(from_l.name, ' -> ', to_l.name) AS route,
                b.name AS bus_name,
                t.departure_time,
                t.arrival_time,
                t.price,
                GREATEST(
                    b.total_seats - COUNT(DISTINCT CASE WHEN active_seat_bookings.id IS NOT NULL THEN active_bd.seat_id END),
                    0
                ) AS available_seats,
                COUNT(DISTINCT active_bookings.id) AS booking_count
            FROM trips t
            INNER JOIN routes r ON r.id = t.route_id
            INNER JOIN locations from_l ON from_l.id = r.from_location_id
            INNER JOIN locations to_l ON to_l.id = r.to_location_id
            INNER JOIN buses b ON b.id = t.bus_id
            LEFT JOIN booking_details active_bd
                ON active_bd.trip_id = t.id
            LEFT JOIN bookings active_seat_bookings
                ON active_seat_bookings.id = active_bd.booking_id
                AND active_seat_bookings.status NOT IN ('cancelled', 'expired')
            LEFT JOIN bookings active_bookings
                ON active_bookings.trip_id = t.id
                AND active_bookings.status IN ('confirmed', 'completed')
            WHERE t.status = 'scheduled'
            GROUP BY
                t.id,
                from_l.name,
                to_l.name,
                b.name,
                b.total_seats,
                t.departure_time,
                t.arrival_time,
                t.price
            ORDER BY {$orderBy}
            LIMIT 3
        ";

        return $this->db()->query($sql)->fetchAll();
    }
}
