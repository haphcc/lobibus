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
            'title' => 'Quan ly thanh toan',
            'payments' => $this->payments->allWithBooking(),
        ], 'admin');
    }

    public function updateStatus(): void
    {
        $this->payments->updateStatus($this->postInt('id'), $this->postString('status', 'pending'));
        $this->redirect('/admin/payments', 'success', 'Payment status updated.');
    }
}
