<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Payment;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Throwable;

final class PaymentController extends Controller
{
    private const ONLINE_METHODS = ['momo', 'zalopay', 'bank_transfer', 'card'];

    public function method(): void
    {
        if (!Auth::check()) {
            $this->redirectLogin();
            return;
        }

        $bookingId = (int) ($_GET['booking_id'] ?? 0);
        $booking = $bookingId > 0 ? (new Booking())->getBookingDetailFull($bookingId) : null;

        if ($booking === null || (int) ($booking['user_id'] ?? 0) !== Auth::id()) {
            Session::flash('error', 'Không tìm thấy booking hoặc bạn không có quyền thanh toán booking này.');
            header('Location: ' . \url('/booking/history'));
            return;
        }

        $payment = (new Payment())->getPaymentByBookingId($bookingId);
        if ($payment === null) {
            Session::flash('error', 'Booking này chưa có thông tin thanh toán.');
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        if (($payment['status'] ?? '') === 'paid') {
            Session::flash('success', 'Booking đã được thanh toán trước đó.');
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        if (!in_array((string) ($payment['method'] ?? ''), self::ONLINE_METHODS, true)) {
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        $this->view('payments.payment-method', [
            'title' => 'Thanh toán đặt vé',
            'booking' => $booking,
            'payment' => $payment,
            'banks' => $this->popularBanks(),
            'paymentQrDataUri' => $this->paymentQrDataUri($booking, $payment),
            'nextTripId' => max(0, (int) ($_GET['next_trip_id'] ?? 0)),
            'nextDirection' => ((string) ($_GET['next_direction'] ?? '') === 'return') ? 'return' : 'outbound',
        ]);
    }

    public function confirm(): void
    {
        if (!Auth::check()) {
            $this->redirectLogin();
            return;
        }

        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $bookingModel = new Booking();
        $paymentModel = new Payment();
        $booking = $bookingId > 0 ? $bookingModel->getBookingDetailFull($bookingId) : null;

        if ($booking === null || (int) ($booking['user_id'] ?? 0) !== Auth::id()) {
            Session::flash('error', 'Không tìm thấy booking hoặc bạn không có quyền thanh toán booking này.');
            header('Location: ' . \url('/booking/history'));
            return;
        }

        $payment = $paymentModel->getPaymentByBookingId($bookingId);
        $method = (string) ($payment['method'] ?? '');

        if ($payment === null || !in_array($method, self::ONLINE_METHODS, true)) {
            Session::flash('error', 'Phương thức thanh toán không hợp lệ.');
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        if (($payment['status'] ?? '') === 'paid') {
            Session::flash('success', 'Booking đã được thanh toán trước đó.');
            header('Location: ' . $this->successRedirect($booking, $_POST));
            return;
        }

        if ($method === 'card' && !$this->validCardPayload($_POST)) {
            Session::flash('error', 'Vui lòng nhập ngân hàng, số thẻ/tài khoản và tên chủ thẻ.');
            header('Location: ' . $this->methodUrl($bookingId, $_POST));
            return;
        }

        $db = Database::connection();
        try {
            $db->beginTransaction();
            $paymentModel->markPaidByBooking($bookingId, $this->generateTransactionCode($method));
            $bookingModel->updateStatus($bookingId, 'confirmed');
            $db->commit();

            Session::flash('success', 'Thanh toán thành công. Vé đã được xác nhận.');
            header('Location: ' . $this->successRedirect($booking, $_POST));
        } catch (Throwable) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            Session::flash('error', 'Không thể xác nhận thanh toán. Vui lòng thử lại.');
            header('Location: ' . $this->methodUrl($bookingId, $_POST));
        }
    }

    public function result(): void
    {
        $bookingId = (int) ($_GET['booking_id'] ?? 0);
        if ($bookingId > 0) {
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        header('Location: ' . \url('/booking/history'));
    }

    private function validCardPayload(array $payload): bool
    {
        $bank = trim((string) ($payload['bank_code'] ?? ''));
        $accountNumber = preg_replace('/\D+/', '', (string) ($payload['account_number'] ?? '')) ?? '';
        $holderName = trim((string) ($payload['account_holder'] ?? ''));

        return $bank !== '' && strlen($accountNumber) >= 6 && $holderName !== '';
    }

    private function methodUrl(int $bookingId, array $source): string
    {
        $query = ['booking_id' => $bookingId];
        if ((int) ($source['next_trip_id'] ?? 0) > 0) {
            $query['next_trip_id'] = (int) $source['next_trip_id'];
            $query['next_direction'] = (string) ($source['next_direction'] ?? 'outbound');
        }

        return \url('/payment/method?' . http_build_query($query));
    }

    private function successRedirect(array $booking, array $source): string
    {
        $nextTripId = (int) ($source['next_trip_id'] ?? 0);
        if ($nextTripId > 0 && ($booking['trip_type'] ?? 'oneway') === 'roundtrip') {
            $query = [
                'trip_id' => $nextTripId,
                'trip_type' => 'roundtrip',
                'direction' => ((string) ($source['next_direction'] ?? '') === 'return') ? 'return' : 'outbound',
                'booking_group_code' => (string) ($booking['booking_group_code'] ?? ''),
            ];

            Session::flash('success', 'Thanh toán thành công. Vui lòng chọn ghế cho chiều còn lại.');
            return \url('/booking/select-seat?' . http_build_query($query));
        }

        return \url('/booking/detail?id=' . (int) ($booking['id'] ?? 0));
    }

    private function generateTransactionCode(string $method): string
    {
        return 'PAY-' . strtoupper($method) . '-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }

    private function paymentQrDataUri(array $booking, array $payment): string
    {
        if (!class_exists(QRCode::class)) {
            return '';
        }

        $payload = json_encode([
            'type' => 'LOBIBUS_DEMO_PAYMENT',
            'booking_code' => (string) ($booking['booking_code'] ?? ''),
            'method' => (string) ($payment['method'] ?? ''),
            'amount' => (float) ($payment['amount'] ?? $booking['total_amount'] ?? 0),
            'content' => 'Thanh toan ' . (string) ($booking['booking_code'] ?? ''),
        ], JSON_UNESCAPED_UNICODE);

        $options = new QROptions();
        $options->outputBase64 = false;
        $options->svgAddXmlHeader = false;
        $options->svgViewBoxSize = 360;
        $options->drawLightModules = true;
        $options->connectPaths = true;

        $svg = (new QRCode($options))->render((string) $payload);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private function popularBanks(): array
    {
        return [
            'VCB' => 'Vietcombank',
            'TCB' => 'Techcombank',
            'BIDV' => 'BIDV',
            'CTG' => 'VietinBank',
            'MB' => 'MB Bank',
            'ACB' => 'ACB',
            'VPB' => 'VPBank',
            'STB' => 'Sacombank',
            'TPB' => 'TPBank',
            'AGR' => 'Agribank',
        ];
    }

    private function redirectLogin(): void
    {
        Session::flash('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        header('Location: ' . \url('/login?redirect=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/booking/history')));
    }
}
