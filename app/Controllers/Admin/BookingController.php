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
            $this->redirect('/admin/bookings', 'error', 'Không tìm thấy đơn đặt vé.');
        }

        $this->view('admin.bookings.detail', [
            'title' => 'Chi tiet dat ve',
            'booking' => $booking,
        ], 'admin');
    }

    public function updateStatus(): void
    {
        $id = $this->postInt('id');
        if ($error = $this->requireAllowed('status', 'Trạng thái đặt vé', ['pending', 'confirmed', 'cancelled', 'completed', 'expired'])) {
            $this->redirect('/admin/bookings/detail?id=' . $id, 'error', $error);
        }

        $this->bookings->updateStatus($id, $this->postString('status', 'pending'));
        $this->redirect('/admin/bookings/detail?id=' . $id, 'success', 'Đã cập nhật trạng thái đặt vé.');
    }
}
