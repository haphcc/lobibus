<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Booking;

final class BookingController extends AdminController
{
    private Booking $bookings;

    public function __construct()
    {
        $this->bookings = new Booking();
    }

    public function index(): void
    {
        $this->view('admin.bookings.index', [
            'title' => 'Quan ly dat ve',
            'bookings' => $this->bookings->allWithDetails(),
        ], 'admin');
    }

    public function detail(): void
    {
        $booking = $this->bookings->findWithDetails($this->queryInt('id'));
        if ($booking === null) {
            $this->redirect('/admin/bookings', 'error', 'Booking not found.');
        }

        $this->view('admin.bookings.detail', [
            'title' => 'Chi tiet dat ve',
            'booking' => $booking,
        ], 'admin');
    }

    public function updateStatus(): void
    {
        $this->bookings->updateStatus($this->postInt('id'), $this->postString('status', 'pending'));
        $this->redirect('/admin/bookings/detail?id=' . $this->postInt('id'), 'success', 'Booking status updated.');
    }
}
