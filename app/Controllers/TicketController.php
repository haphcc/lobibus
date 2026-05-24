<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Ticket;

final class TicketController extends Controller
{
    public function showQr(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Vui lòng đăng nhập để xem mã QR vé.');
            header('Location: ' . \url('/login?redirect=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/ticket/qr')));
            return;
        }

        $ticketCode = trim((string) ($_GET['ticket_code'] ?? ''));
        $ticket = $ticketCode !== '' ? (new Ticket())->getTicketByCode($ticketCode) : null;

        if ($ticket === null || (int) ($ticket['user_id'] ?? 0) !== Auth::id()) {
            http_response_code(404);
            $this->view('tickets.qr', [
                'title' => 'Mã QR vé',
                'ticket' => null,
                'message' => 'Không tìm thấy vé hoặc bạn không có quyền xem vé này.',
            ]);
            return;
        }

        $this->view('tickets.qr', [
            'title' => 'Mã QR vé',
            'ticket' => $ticket,
        ]);
    }
}
