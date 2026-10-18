<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class BookingController extends Controller
{
    public function selectSeat(): void
    {
        $this->view('bookings.select-seat', ['title' => 'Chọn ghế']);
    }

    public function checkout(): void
    {
        $this->view('bookings.checkout', ['title' => 'Thanh toán']);
    }

    public function history(): void
    {
        $this->view('bookings.history', ['title' => 'Lịch sử đặt vé']);
    }

    public function detail(): void
    {
        $this->view('bookings.detail', ['title' => 'Chi tiết đặt vé']);
    }

    public function cancel(): void
    {
        // TODO: cancel booking through BookingService.
        header('Location: /booking/history');
    }
}
