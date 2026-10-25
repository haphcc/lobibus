<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Payment extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO payments (booking_id, method, amount, status, transaction_code)
             VALUES (:booking_id, :method, :amount, :status, :transaction_code)'
        );
        $stmt->execute([
            'booking_id' => (int) ($data['booking_id'] ?? 0),
            'method' => (string) ($data['method'] ?? 'cash'),
            'amount' => (float) ($data['amount'] ?? 0),
            'status' => (string) ($data['status'] ?? 'pending'),
            'transaction_code' => $data['transaction_code'] ?? null,
        ]);
        return (int) $this->db()->lastInsertId();
    }

    public function allWithBooking(): array
    {
        $stmt = $this->db()->query(
            'SELECT p.*, b.booking_code, b.customer_name, b.customer_phone
             FROM payments p
             JOIN bookings b ON b.id = p.booking_id
             ORDER BY p.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db()->prepare('UPDATE payments SET status = :status WHERE id = :id');
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }
}
