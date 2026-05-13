<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class PaymentController extends Controller
{
    public function method(): void
    {
        $this->view('payments.payment-method', ['title' => 'Phương thức thanh toán']);
    }

    public function result(): void
    {
        $this->view('payments.result', ['title' => 'Kết quả thanh toán']);
    }
}
