<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class TicketController extends Controller
{
    public function showQr(): void
    {
        $this->view('tickets.qr', ['title' => 'Mã QR vé']);
    }
}
