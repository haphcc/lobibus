<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use App\Services\PayOSService;
use App\Services\QRCodeService;
use App\Services\TicketEmailService;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Throwable;

final class PaymentController extends Controller
{
    private const ONLINE_METHODS = ['momo', 'zalopay', 'bank_transfer', 'card'];

    public function method(): void
    {
        $bookingId = (int) ($_GET['booking_id'] ?? 0);
        $booking = $bookingId > 0 ? (new Booking())->getBookingDetailFull($bookingId) : null;

        $isAuthorized = false;
        if ($booking !== null) {
            $guestBookings = Session::get('guest_bookings');
            $isInGuestSession = is_array($guestBookings) && in_array($bookingId, $guestBookings, true);
            if (Auth::check()) {
                $isAuthorized = ((int) ($booking['user_id'] ?? 0) === Auth::id()) || $isInGuestSession;
            } else {
                $isAuthorized = $isInGuestSession;
            }
        }

        if (!$isAuthorized) {
            Session::flash('error', 'Khong tim thay booking hoac ban khong co quyen thanh toan booking nay.');
            if (Auth::check()) {
                header('Location: ' . \url('/booking/history'));
            } else {
                header('Location: ' . \url('/'));
            }
            return;
        }

        $payment = (new Payment())->getPaymentByBookingId($bookingId);
        if ($payment === null) {
            Session::flash('error', 'Booking nay chua co thong tin thanh toan.');
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        if (($payment['status'] ?? '') === 'paid') {
            Session::flash('success', 'Booking da duoc thanh toan truoc do.');
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        if (!in_array((string) ($payment['method'] ?? ''), self::ONLINE_METHODS, true)) {
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        $payosData = [];
        $payosError = '';
        try {
            $payosData = (new PayOSService())->createPaymentLink(
                $booking,
                $payment,
                $this->absoluteUrl('/payment/result?booking_id=' . $bookingId),
                $this->absoluteUrl('/booking/detail?id=' . $bookingId)
            );
        } catch (Throwable $exception) {
            $payosError = $exception->getMessage();
        }
        $payosData = $this->payosDataWithCachedQr($bookingId, $payosData);

        $this->view('payments.payment-method', [
            'title' => 'Thanh toan dat ve',
            'booking' => $booking,
            'payment' => $payment,
            'paymentQrDataUri' => $this->paymentQrDataUri((string) ($payosData['qrCode'] ?? '')),
            'payosData' => $payosData,
            'payosError' => $payosError,
            'nextTripId' => max(0, (int) ($_GET['next_trip_id'] ?? 0)),
            'nextDirection' => ((string) ($_GET['next_direction'] ?? '') === 'return') ? 'return' : 'outbound',
        ]);
    }

    public function confirm(): void
    {
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $bookingModel = new Booking();
        $paymentModel = new Payment();
        $booking = $bookingId > 0 ? $bookingModel->getBookingDetailFull($bookingId) : null;

        $isAuthorized = false;
        if ($booking !== null) {
            $guestBookings = Session::get('guest_bookings');
            $isInGuestSession = is_array($guestBookings) && in_array($bookingId, $guestBookings, true);
            if (Auth::check()) {
                $isAuthorized = ((int) ($booking['user_id'] ?? 0) === Auth::id()) || $isInGuestSession;
            } else {
                $isAuthorized = $isInGuestSession;
            }
        }

        if (!$isAuthorized) {
            Session::flash('error', 'Khong tim thay booking hoac ban khong co quyen thanh toan booking nay.');
            if (Auth::check()) {
                header('Location: ' . \url('/booking/history'));
            } else {
                header('Location: ' . \url('/'));
            }
            return;
        }

        $payment = $paymentModel->getPaymentByBookingId($bookingId);
        $method = (string) ($payment['method'] ?? '');

        if ($payment === null || !in_array($method, self::ONLINE_METHODS, true)) {
            Session::flash('error', 'Phuong thuc thanh toan khong hop le.');
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        if (($payment['status'] ?? '') === 'paid') {
            Session::flash('success', 'Booking da duoc thanh toan truoc do.');
            header('Location: ' . $this->successRedirect($booking, $_POST));
            return;
        }

        try {
            $payos = new PayOSService();
            $payosStatus = $payos->getPaymentRequest($payos->orderCodeForBooking($bookingId));
        } catch (Throwable $exception) {
            Session::flash('error', $exception->getMessage());
            header('Location: ' . $this->methodUrl($bookingId, $_POST));
            return;
        }

        if (($payosStatus['status'] ?? '') !== 'PAID') {
            Session::flash('error', 'payOS chua ghi nhan thanh toan thanh cong. Vui long quet QR hoac bam kiem tra lai sau.');
            header('Location: ' . $this->methodUrl($bookingId, $_POST));
            return;
        }

        $db = Database::connection();
        try {
            $db->beginTransaction();
            $paymentModel->markPaidByBooking($bookingId, 'PAYOS-' . (string) ($payosStatus['orderCode'] ?? $payos->orderCodeForBooking($bookingId)));
            $bookingModel->updateStatus($bookingId, 'confirmed');
            Session::forget('payos_payment_' . $bookingId);
            $db->commit();
            $this->refreshConfirmedTicketAndEmail($bookingId);

            Session::flash('success', 'Thanh toan thanh cong. Ve da duoc xac nhan.');
            header('Location: ' . $this->successRedirect($booking, $_POST));
        } catch (Throwable) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            Session::flash('error', 'Khong the xac nhan thanh toan. Vui long thu lai.');
            header('Location: ' . $this->methodUrl($bookingId, $_POST));
        }
    }

    public function result(): void
    {
        $bookingId = (int) ($_GET['booking_id'] ?? 0);
        if ($bookingId > 0) {
            $bookingModel = new Booking();
            $paymentModel = new Payment();
            $booking = $bookingModel->getBookingDetailFull($bookingId);
            $payment = $paymentModel->getPaymentByBookingId($bookingId);

            $isAuthorized = false;
            if ($booking !== null) {
                $guestBookings = Session::get('guest_bookings');
                $isInGuestSession = is_array($guestBookings) && in_array($bookingId, $guestBookings, true);
                if (Auth::check()) {
                    $isAuthorized = ((int) ($booking['user_id'] ?? 0) === Auth::id()) || $isInGuestSession;
                } else {
                    $isAuthorized = $isInGuestSession;
                }
            }

            if ($isAuthorized && $payment !== null) {
                try {
                    $payos = new PayOSService();
                    $payosStatus = $payos->getPaymentRequest($payos->orderCodeForBooking($bookingId));
                } catch (Throwable) {
                    $payosStatus = [];
                }
                if (($payosStatus['status'] ?? '') === 'PAID' && ($payment['status'] ?? '') !== 'paid') {
                    $paymentModel->markPaidByBooking($bookingId, 'PAYOS-' . (string) ($payosStatus['orderCode'] ?? $payos->orderCodeForBooking($bookingId)));
                    $bookingModel->updateStatus($bookingId, 'confirmed');
                    Session::forget('payos_payment_' . $bookingId);
                    $this->refreshConfirmedTicketAndEmail($bookingId);
                    Session::flash('success', 'Thanh toan payOS thanh cong. Ve da duoc xac nhan.');
                }
            }

            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        header('Location: ' . \url('/booking/history'));
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

            Session::flash('success', 'Thanh toan thanh cong. Vui long chon ghe cho chieu con lai.');
            return \url('/booking/select-seat?' . http_build_query($query));
        }

        return \url('/booking/detail?id=' . (int) ($booking['id'] ?? 0));
    }

    private function paymentQrDataUri(string $qrCode): string
    {
        if ($qrCode === '' || !class_exists(QRCode::class) || !class_exists(QROptions::class)) {
            return '';
        }

        $options = new QROptions();
        $options->outputBase64 = false;
        $options->svgAddXmlHeader = false;
        $options->svgViewBoxSize = 360;
        $options->drawLightModules = true;
        $options->connectPaths = true;

        $svg = (new QRCode($options))->render($qrCode);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private function refreshConfirmedTicketAndEmail(int $bookingId): void
    {
        $booking = (new Booking())->getBookingDetailFull($bookingId);
        $ticket = (new Ticket())->getTicketByBooking($bookingId);
        if ($booking === null || $ticket === null) {
            return;
        }

        $seatNumbers = implode(', ', array_column($booking['seats'] ?? [], 'seat_number'));
        $payload = json_encode([
            'ticket_code' => (string) ($ticket['ticket_code'] ?? ''),
            'booking_id' => $bookingId,
            'booking_code' => (string) ($booking['booking_code'] ?? ''),
            'booking_group_code' => $booking['booking_group_code'] ?? null,
            'trip_type' => (string) ($booking['trip_type'] ?? 'oneway'),
            'direction' => (string) ($booking['direction'] ?? 'outbound'),
            'booking_status' => 'confirmed',
            'customer' => [
                'name' => (string) ($booking['customer_name'] ?? ''),
                'phone' => (string) ($booking['customer_phone'] ?? ''),
                'email' => $booking['customer_email'] ?? null,
            ],
            'trip' => [
                'id' => (int) ($booking['trip_id'] ?? 0),
                'from' => (string) ($booking['from_name'] ?? ''),
                'to' => (string) ($booking['to_name'] ?? ''),
                'bus' => (string) ($booking['bus_name'] ?? ''),
                'departure_time' => (string) ($booking['departure_time'] ?? ''),
                'arrival_time' => (string) ($booking['arrival_time'] ?? ''),
            ],
            'seats' => $seatNumbers,
            'total_amount' => (float) ($booking['total_amount'] ?? 0),
            'issued_at' => date('c'),
        ], JSON_UNESCAPED_UNICODE);

        try {
            $qrPath = (new QRCodeService())->generate((string) $payload, (string) ($ticket['ticket_code'] ?? ''));
            (new Ticket())->updateQrPath((int) ($ticket['id'] ?? 0), $qrPath);
            (new TicketEmailService())->sendForBooking($bookingId);
        } catch (Throwable) {
            // Email/QR refresh must not block successful payment confirmation.
        }
    }

    private function payosDataWithCachedQr(int $bookingId, array $payosData): array
    {
        $cacheKey = 'payos_payment_' . $bookingId;
        $cached = Session::get($cacheKey);
        $cached = is_array($cached) ? $cached : [];

        if (empty($payosData['qrCode']) && !empty($cached['qrCode'])) {
            $payosData['qrCode'] = $cached['qrCode'];
        }

        if (empty($payosData['checkoutUrl']) && !empty($cached['checkoutUrl'])) {
            $payosData['checkoutUrl'] = $cached['checkoutUrl'];
        }

        if (empty($payosData['orderCode']) && !empty($cached['orderCode'])) {
            $payosData['orderCode'] = $cached['orderCode'];
        }

        if (empty($payosData['description']) && !empty($cached['description'])) {
            $payosData['description'] = $cached['description'];
        }

        if (!empty($payosData['qrCode'])) {
            Session::set($cacheKey, array_merge($cached, $payosData));
        }

        return $payosData;
    }

    private function absoluteUrl(string $path): string
    {
        $url = \url($path);
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . $url;
    }

    private function redirectLogin(): void
    {
        Session::flash('error', 'Vui long dang nhap de tiep tuc thanh toan.');
        header('Location: ' . \url('/login?redirect=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/booking/history')));
    }
}
