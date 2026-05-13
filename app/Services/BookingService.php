<?php
declare(strict_types=1);
namespace App\Services;

final class BookingService
{
    public function create(array $data): int
    {
        // TODO: wrap booking, details, ticket, payment creation in transaction.
        return 0;
    }
}
