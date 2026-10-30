<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Statistic extends Model
{
    public function dashboardSummary(): array
    {
        return [
            'users' => $this->countTable('users'),
            'trips' => $this->countTable('trips'),
            'bookings' => $this->countTable('bookings'),
            'tickets' => $this->countTable('tickets'),
            'revenue' => $this->paidRevenue(),
        ];
    }

    public function revenueByDay(int $days = 7): array
    {
        $days = max(1, min($days, 365));
        $statement = $this->db()->query("
            SELECT DATE(t.departure_time) AS label, COALESCE(SUM(p.amount), 0) AS revenue
            FROM payments p
            INNER JOIN bookings b ON b.id = p.booking_id
            INNER JOIN trips t ON t.id = b.trip_id
            WHERE p.status = 'paid'
                AND t.departure_time >= CURDATE()
                AND t.departure_time < DATE_ADD(CURDATE(), INTERVAL {$days} DAY)
            GROUP BY DATE(t.departure_time)
            ORDER BY label ASC
        ");

        return $statement->fetchAll();
    }

    public function bookingStatusBreakdown(): array
    {
        $statement = $this->db()->query("
            SELECT status AS label, COUNT(*) AS total
            FROM bookings
            GROUP BY status
            ORDER BY status ASC
        ");

        return $statement->fetchAll();
    }

    public function paymentMethodBreakdown(): array
    {
        $statement = $this->db()->query("
            SELECT method AS label, COUNT(*) AS total, COALESCE(SUM(amount), 0) AS revenue
            FROM payments
            GROUP BY method
            ORDER BY revenue DESC
        ");

        return $statement->fetchAll();
    }

    public function tripStatusBreakdown(): array
    {
        $statement = $this->db()->query("
            SELECT status AS label, COUNT(*) AS total
            FROM trips
            GROUP BY status
            ORDER BY status ASC
        ");

        return $statement->fetchAll();
    }

    public function usersByRole(): array
    {
        $statement = $this->db()->query("
            SELECT r.name AS label, COUNT(u.id) AS total
            FROM roles r
            LEFT JOIN users u ON u.role_id = r.id
            GROUP BY r.id, r.name
            HAVING total > 0
            ORDER BY total DESC, r.name ASC
        ");

        return $statement->fetchAll();
    }

    public function topRoutes(int $limit = 5): array
    {
        $limit = max(1, min($limit, 20));
        $statement = $this->db()->query("
            SELECT
                CONCAT(from_l.name, ' -> ', to_l.name) AS route,
                COUNT(bk.id) AS bookings,
                COALESCE(SUM(CASE WHEN p.status = 'paid' THEN p.amount ELSE 0 END), 0) AS revenue
            FROM bookings bk
            INNER JOIN trips t ON t.id = bk.trip_id
            INNER JOIN routes r ON r.id = t.route_id
            INNER JOIN locations from_l ON from_l.id = r.from_location_id
            INNER JOIN locations to_l ON to_l.id = r.to_location_id
            LEFT JOIN payments p ON p.booking_id = bk.id
            GROUP BY r.id, from_l.name, to_l.name
            ORDER BY bookings DESC, revenue DESC
            LIMIT {$limit}
        ");

        return $statement->fetchAll();
    }

    public function upcomingTrips(int $limit = 6): array
    {
        $limit = max(1, min($limit, 20));
        $statement = $this->db()->query("
            SELECT
                t.id,
                CONCAT(from_l.name, ' -> ', to_l.name) AS route,
                b.name AS bus_name,
                t.departure_time,
                t.arrival_time,
                t.price,
                t.status,
                GREATEST(
                    b.total_seats - COUNT(DISTINCT CASE WHEN bk.status NOT IN ('cancelled', 'expired') THEN bd.seat_id END),
                    0
                ) AS available_seats
            FROM trips t
            INNER JOIN routes r ON r.id = t.route_id
            INNER JOIN locations from_l ON from_l.id = r.from_location_id
            INNER JOIN locations to_l ON to_l.id = r.to_location_id
            INNER JOIN buses b ON b.id = t.bus_id
            LEFT JOIN booking_details bd ON bd.trip_id = t.id
            LEFT JOIN bookings bk ON bk.id = bd.booking_id
            WHERE t.departure_time >= NOW()
            GROUP BY
                t.id,
                from_l.name,
                to_l.name,
                b.name,
                b.total_seats,
                t.departure_time,
                t.arrival_time,
                t.price,
                t.status
            ORDER BY t.departure_time ASC
            LIMIT {$limit}
        ");

        return $statement->fetchAll();
    }

    public function recentBookings(int $limit = 8): array
    {
        $limit = max(1, min($limit, 30));
        $statement = $this->db()->query("
            SELECT
                bk.booking_code,
                bk.customer_name,
                bk.customer_phone,
                bk.total_amount,
                bk.status,
                bk.created_at,
                CONCAT(from_l.name, ' -> ', to_l.name) AS route,
                p.status AS payment_status
            FROM bookings bk
            INNER JOIN trips t ON t.id = bk.trip_id
            INNER JOIN routes r ON r.id = t.route_id
            INNER JOIN locations from_l ON from_l.id = r.from_location_id
            INNER JOIN locations to_l ON to_l.id = r.to_location_id
            LEFT JOIN payments p ON p.booking_id = bk.id
            ORDER BY bk.created_at DESC, bk.id DESC
            LIMIT {$limit}
        ");

        return $statement->fetchAll();
    }

    private function countTable(string $table): int
    {
        $allowed = ['users', 'trips', 'bookings', 'tickets'];
        if (!in_array($table, $allowed, true)) {
            throw new \InvalidArgumentException('Unsupported table for statistics.');
        }

        return (int) $this->db()->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
    }

    private function paidRevenue(): float
    {
        $statement = $this->db()->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid'");

        return (float) $statement->fetchColumn();
    }
}
