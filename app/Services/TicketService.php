<?php
declare(strict_types=1);
namespace App\Services;

final class TicketService
{
    public function generateCode(): string
    {
        return 'LB' . date('YmdHis');
    }
}
