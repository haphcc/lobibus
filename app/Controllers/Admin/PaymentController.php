<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Payment;

final class PaymentController extends AdminController
{
    private Payment $payments;

    public function __construct()
    {
        $this->payments = new Payment();
    }

    public function index(): void
    {
        $this->view('admin.payments.index', [
            'title' => 'Quản lý thanh toán',
            'payments' => $this->payments->allWithBooking(),
        ], 'admin');
    }

    public function updateStatus(): void
    {
        if ($error = $this->requireAllowed('status', 'Trạng thái thanh toán', ['pending', 'paid', 'failed', 'refunded', 'cancelled'])) {
            $this->redirect('/admin/payments', 'error', $error);
        }

        $this->payments->updateStatus($this->postInt('id'), $this->postString('status', 'pending'));
        $this->redirect('/admin/payments', 'success', 'Đã cập nhật trạng thái thanh toán.');
    }
}
