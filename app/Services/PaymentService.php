<?php
declare(strict_types=1);
namespace App\Services;

final class PaymentService
{
    public function createPendingPayment(int $bookingId, string $method): array
    {
        return ['booking_id' => $bookingId, 'method' => $method, 'status' => 'pending'];
    }
}
