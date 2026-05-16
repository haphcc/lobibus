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
            'revenue' => $this->paidRevenue(),
        ];
    }

    private function countTable(string $table): int
    {
        $stmt = $this->db()->query("SELECT COUNT(*) FROM {$table}");
        return (int) $stmt->fetchColumn();
    }

    private function paidRevenue(): float
    {
        $stmt = $this->db()->query('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = "paid"');
        return (float) $stmt->fetchColumn();
    }
}
