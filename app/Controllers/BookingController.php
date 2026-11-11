<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\Trip;
use App\Services\QRCodeService;
use App\Services\TicketEmailService;
use PDOException;
use Throwable;

final class BookingController extends Controller
{
    public function selectSeat(): void
    {
        $tripId = (int) ($_GET['trip_id'] ?? 0);
        $bookingMeta = $this->bookingMetaFromRequest($_GET);
        $trip = $tripId > 0 ? (new Trip())->findWithDetails($tripId) : null;

        if ($trip === null || ($trip['status'] ?? '') !== 'scheduled') {
            Session::flash('error', 'Chuyến xe không tồn tại hoặc không còn mở bán vé.');
            header('Location: ' . \url('/trips/search'));
            return;
        }

        $this->view('bookings.select-seat', [
            'title' => 'Chọn ghế',
            'trip' => $trip,
            'bookingMeta' => $bookingMeta,
            'seatCount' => $this->seatCountFromMeta($bookingMeta),
        ]);
    }

    public function checkout(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->prepareCheckout();
            return;
        }

        $checkout = Session::get('booking_checkout');
        if (!is_array($checkout)) {
            Session::flash('error', 'Phiên chọn ghế đã hết hạn. Vui lòng chọn lại ghế.');
            header('Location: ' . \url('/trips/search'));
            return;
        }

        $tripId = (int) ($checkout['trip_id'] ?? 0);
        $bookingMeta = $this->bookingMetaFromRequest($checkout);
        $seatIds = $this->seatIdsFromRequest($checkout['seat_ids'] ?? []);
        $legs = is_array($checkout['legs'] ?? null) ? $checkout['legs'] : [];
        if ($legs !== []) {
            $this->checkoutRoundTrip($legs, $bookingMeta);
            return;
        }
        $trip = $tripId > 0 ? (new Trip())->findWithDetails($tripId) : null;

        if ($trip === null || ($trip['status'] ?? '') !== 'scheduled') {
            Session::flash('error', 'Chuyến xe không tồn tại hoặc không còn mở bán vé.');
            header('Location: ' . \url('/trips/search'));
            return;
        }

        if ($seatIds === []) {
            Session::flash('error', 'Vui lòng chọn ít nhất một ghế.');
            header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
            return;
        }

        $seats = (new Seat())->getAvailableSeatsForTrip($tripId, $seatIds);
        if (count($seats) !== count(array_unique($seatIds))) {
            Session::flash('error', 'Một hoặc nhiều ghế vừa được đặt. Vui lòng chọn lại.');
            header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
            return;
        }

        $this->view('bookings.checkout', [
            'title' => 'Xác nhận đặt vé',
            'trip' => $trip,
            'seats' => $seats,
            'totalAmount' => array_sum(array_map(static fn (array $seat): float => (float) $seat['price'], $seats)),
            'user' => Auth::user(),
            'bookingMeta' => $bookingMeta,
        ]);
    }

    public function store(): void
    {
        $tripId = (int) ($_POST['trip_id'] ?? 0);
        $bookingMeta = $this->bookingMetaFromRequest($_POST);
        $seatIds = $this->seatIdsFromRequest($_POST['seat_ids'] ?? $_POST['seats'] ?? []);
        $legs = is_array($_POST['legs'] ?? null) ? $_POST['legs'] : [];
        
        $currentUser = Auth::user();
        $customerName = trim((string) ($_POST['customer_name'] ?? ($currentUser ? $currentUser['name'] : '') ?? ''));
        $customerPhone = trim((string) ($_POST['customer_phone'] ?? ($currentUser ? $currentUser['phone'] : '') ?? ''));
        $customerEmail = trim((string) ($_POST['customer_email'] ?? ($currentUser ? $currentUser['email'] : '') ?? ''));
        $paymentMethod = $this->paymentMethodFromRequest($_POST['payment_method'] ?? 'cash');

        if ($legs !== []) {
            $this->storeRoundTrip($legs, $bookingMeta, $customerName, $customerPhone, $customerEmail, $paymentMethod);
            return;
        }

        if ($tripId <= 0 || $seatIds === [] || $customerName === '' || $customerPhone === '' || $customerEmail === '') {
            Session::flash('error', 'Thông tin đặt vé không hợp lệ. Vui lòng điền đầy đủ họ tên, số điện thoại và email.');
            header('Location: ' . $this->selectSeatUrl(max(0, $tripId), $bookingMeta));
            return;
        }

        if (count($seatIds) !== $this->seatCountFromMeta($bookingMeta)) {
            Session::flash('error', 'Vui lòng chọn đúng ' . $this->seatCountFromMeta($bookingMeta) . ' ghế.');
            header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
            return;
        }

        $trip = (new Trip())->findWithDetails($tripId);
        if ($trip === null || ($trip['status'] ?? '') !== 'scheduled') {
            Session::flash('error', 'Chuyến xe không tồn tại hoặc không còn mở bán vé.');
            header('Location: ' . \url('/trips/search'));
            return;
        }

        $db = Database::connection();
        $bookingModel = new Booking();
        $detailModel = new BookingDetail();
        $seatModel = new Seat();
        $ticketModel = new Ticket();
        $paymentModel = new Payment();
        $tripModel = new Trip();
        $qrService = new QRCodeService();

        try {
            $db->beginTransaction();

            // Server-side validation is repeated inside the transaction to avoid double booking.
            $seatModel->lockSeatsForTrip($tripId, $seatIds);
            $seats = $seatModel->getAvailableSeatsForTrip($tripId, $seatIds);
            if (count($seats) !== count(array_unique($seatIds))) {
                throw new \RuntimeException('Một hoặc nhiều ghế vừa được đặt. Vui lòng chọn lại.');
            }

            if (!$tripModel->decrementAvailableSeats($tripId, count($seats))) {
                throw new \RuntimeException('Số ghế trống không đủ. Vui lòng chọn lại.');
            }

            $totalAmount = array_sum(array_map(static fn (array $seat): float => (float) $seat['price'], $seats));
            $bookingCode = $bookingModel->generateBookingCode();
            $bookingId = $bookingModel->createBooking([
                'user_id' => Auth::id(),
                'trip_id' => $tripId,
                'booking_code' => $bookingCode,
                'booking_group_code' => $bookingMeta['booking_group_code'],
                'trip_type' => $bookingMeta['trip_type'],
                'direction' => $bookingMeta['direction'],
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail !== '' ? $customerEmail : null,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($seats as $seat) {
                $detailModel->createDetail($bookingId, $tripId, (int) $seat['id'], (float) $seat['price']);
            }

            $paymentModel->createPayment([
                'booking_id' => $bookingId,
                'method' => $paymentMethod,
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);

            $ticketCode = $ticketModel->generateTicketCode();
            $seatNumbers = implode(', ', array_column($seats, 'seat_number'));
            $qrPayload = json_encode($this->ticketQrPayload(
                $ticketCode,
                $bookingCode,
                $bookingId,
                $trip,
                $bookingMeta,
                $seatNumbers,
                $customerName,
                $customerPhone,
                $customerEmail !== '' ? $customerEmail : null,
                $totalAmount,
                'pending'
            ), JSON_UNESCAPED_UNICODE);
            $qrPath = $qrService->generate((string) $qrPayload, $ticketCode);

            $ticketModel->createTicket([
                'booking_id' => $bookingId,
                'ticket_code' => $ticketCode,
                'qr_code_path' => $qrPath,
                'status' => 'valid',
            ]);

            $db->commit();
            Session::forget('booking_checkout');
            
            // Save guest booking to session
            $guestBookings = Session::get('guest_bookings');
            $guestBookings = is_array($guestBookings) ? $guestBookings : [];
            $guestBookings[] = (int) $bookingId;
            Session::set('guest_bookings', $guestBookings);

            $this->sendTicketEmails([$bookingId]);
            if ($this->requiresOnlinePayment($paymentMethod)) {
                $query = ['booking_id' => $bookingId];
                if ($bookingMeta['trip_type'] === 'roundtrip' && (int) ($bookingMeta['next_trip_id'] ?? 0) > 0) {
                    $query['next_trip_id'] = (int) $bookingMeta['next_trip_id'];
                    $query['next_direction'] = (string) ($bookingMeta['next_direction'] ?? ($bookingMeta['direction'] === 'return' ? 'outbound' : 'return'));
                }

                Session::flash('success', 'Đặt vé thành công. Vui lòng xác nhận thanh toán.');
                header('Location: ' . \url('/payment/method?' . http_build_query($query)));
                return;
            }
            Session::flash('success', 'Đặt vé thành công. Vé đang chờ thanh toán.');
            if ($bookingMeta['trip_type'] === 'roundtrip' && (int) ($bookingMeta['next_trip_id'] ?? 0) > 0) {
                Session::flash('success', 'Đã đặt xong ' . ($bookingMeta['direction'] === 'return' ? 'chiều về' : 'chiều đi') . '. Vui lòng chọn ghế cho chiều còn lại.');
                header('Location: ' . $this->selectSeatUrl((int) $bookingMeta['next_trip_id'], [
                    'trip_type' => 'roundtrip',
                    'direction' => (string) ($bookingMeta['next_direction'] ?? ($bookingMeta['direction'] === 'return' ? 'outbound' : 'return')),
                    'booking_group_code' => $bookingMeta['booking_group_code'],
                ]));
                return;
            }
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
        } catch (PDOException $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $message = str_contains($exception->getMessage(), 'uq_trip_seat')
                || str_contains($exception->getMessage(), 'idx_booking_details_trip_seat')
                ? 'Một hoặc nhiều ghế vừa được đặt. Vui lòng chọn lại.'
                : 'Không thể tạo booking. Vui lòng thử lại.';
            Session::flash('error', $message);
            header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            Session::flash('error', $exception->getMessage());
            header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
        }
    }

    public function history(): void
    {
        $bookingModel = new Booking();
        
        // Handle search query
        $searchCode = trim((string) ($_GET['booking_code'] ?? ''));
        if ($searchCode !== '') {
            $searchCode = strtoupper($searchCode);
            $booking = $bookingModel->getBookingByCode($searchCode);
            if ($booking !== null) {
                // Authorize user to view this booking detail in their session
                $guestBookings = Session::get('guest_bookings');
                $guestBookings = is_array($guestBookings) ? $guestBookings : [];
                if (!in_array((int) $booking['id'], $guestBookings, true)) {
                    $guestBookings[] = (int) $booking['id'];
                    Session::set('guest_bookings', $guestBookings);
                }
                
                // Redirect to detail page
                Session::flash('success', 'Tìm thấy thông tin đặt vé!');
                header('Location: ' . \url('/booking/detail?id=' . (int) $booking['id']));
                return;
            } else {
                Session::flash('error', 'Mã đặt vé không tồn tại hoặc không hợp lệ. Vui lòng kiểm tra lại.');
                header('Location: ' . \url('/booking/history'));
                return;
            }
        }

        $bookings = [];
        if (Auth::check()) {
            $bookings = $bookingModel->getBookingsByUser((int) Auth::id());
            foreach ($bookings as &$booking) {
                $booking['can_cancel'] = $bookingModel->canCancel($booking);
                $booking['cancel_reason'] = $bookingModel->cancelReason($booking);
            }
            unset($booking);
        }

        $this->view('bookings.history', [
            'title' => 'Tra cứu vé',
            'bookings' => $bookings,
        ]);
    }

    public function detail(): void
    {
        $bookingId = (int) ($_GET['id'] ?? $_GET['booking_id'] ?? 0);
        $bookingModel = new Booking();
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
            http_response_code(404);
            $this->view('bookings.detail', [
                'title' => 'Chi tiết đặt vé',
                'booking' => null,
                'message' => 'Không tìm thấy booking hoặc bạn không có quyền xem booking này.',
            ]);
            return;
        }

        $booking['can_cancel'] = $bookingModel->canCancel($booking);
        $booking['cancel_reason'] = $bookingModel->cancelReason($booking);
        $this->view('bookings.detail', [
            'title' => 'Chi tiết đặt vé',
            'booking' => $booking,
        ]);
    }

    public function cancel(): void
    {
        $bookingId = (int) ($_POST['booking_id'] ?? $_GET['id'] ?? 0);
        $bookingModel = new Booking();
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
            Session::flash('error', 'Không tìm thấy booking hoặc bạn không có quyền hủy booking này.');
            if (Auth::check()) {
                header('Location: ' . \url('/booking/history'));
            } else {
                header('Location: ' . \url('/'));
            }
            return;
        }

        if (!$bookingModel->canCancel($booking)) {
            Session::flash('error', 'Không thể hủy vé sau khi xe đã khởi hành hoặc vé đã bị hủy.');
            header('Location: ' . \url('/booking/detail?id=' . $bookingId));
            return;
        }

        $db = Database::connection();
        try {
            $db->beginTransaction();
            $details = (new BookingDetail())->getDetailsByBookingId($bookingId);
            $bookingModel->updateStatus($bookingId, 'cancelled');
            (new Ticket())->updateStatus($bookingId, 'cancelled');
            (new Payment())->updateStatusByBooking($bookingId, 'cancelled');
            (new Trip())->incrementAvailableSeats((int) $booking['trip_id'], count($details));
            $db->commit();

            Session::flash('success', 'Đã hủy vé thành công.');
        } catch (Throwable) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            Session::flash('error', 'Không thể hủy vé. Vui lòng thử lại.');
        }

        header('Location: ' . \url('/booking/detail?id=' . $bookingId));
    }

    private function redirectLogin(): void
    {
        Session::flash('error', 'Vui lòng đăng nhập để tiếp tục đặt vé.');
        header('Location: ' . \url('/login?redirect=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/booking/history')));
    }

    private function seatIdsFromRequest(mixed $input): array
    {
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            $input = is_array($decoded) ? $decoded : explode(',', $input);
        }

        if (!is_array($input)) {
            return [];
        }

        $seatIds = array_values(array_unique(array_filter(array_map('intval', $input), static fn (int $id): bool => $id > 0)));
        return array_slice($seatIds, 0, 5);
    }

    private function seatCountFromMeta(array $bookingMeta): int
    {
        return max(1, min(5, (int) ($bookingMeta['seats'] ?? 1)));
    }

    private function prepareCheckout(): void
    {
        $tripId = (int) ($_POST['trip_id'] ?? 0);
        $bookingMeta = $this->bookingMetaFromRequest($_POST);
        $seatIds = $this->seatIdsFromRequest($_POST['seat_ids'] ?? $_POST['seats'] ?? []);
        $trip = $tripId > 0 ? (new Trip())->findWithDetails($tripId) : null;
        $expectedSeats = $this->seatCountFromMeta($bookingMeta);

        if ($trip === null || ($trip['status'] ?? '') !== 'scheduled') {
            Session::flash('error', 'Chuyến xe không tồn tại hoặc không còn mở bán vé.');
            header('Location: ' . \url('/trips/search'));
            return;
        }

        if ($seatIds === []) {
            Session::flash('error', 'Vui lòng chọn ít nhất một ghế.');
            header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
            return;
        }

        if (count($seatIds) !== $expectedSeats) {
            Session::flash('error', 'Vui lòng chọn đúng ' . $expectedSeats . ' ghế.');
            header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
            return;
        }

        $seats = (new Seat())->getAvailableSeatsForTrip($tripId, $seatIds);
        if (count($seats) !== count(array_unique($seatIds))) {
            Session::flash('error', 'Một hoặc nhiều ghế vừa được đặt. Vui lòng chọn lại.');
            header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
            return;
        }

        if ($bookingMeta['trip_type'] === 'roundtrip' && (int) ($bookingMeta['next_trip_id'] ?? 0) > 0) {
            $currentLeg = [
                'trip_id' => $tripId,
                'seat_ids' => $seatIds,
                'direction' => $bookingMeta['direction'],
                'booking_group_code' => $bookingMeta['booking_group_code'],
            ];
            $pending = Session::get('roundtrip_pending');

            if (is_array($pending)
                && ($pending['booking_group_code'] ?? null) === $bookingMeta['booking_group_code']
                && (string) ($pending['direction'] ?? '') !== $bookingMeta['direction']
            ) {
                $legs = [$pending, $currentLeg];
                usort($legs, static fn (array $a, array $b): int => (($a['direction'] ?? '') === 'outbound' ? 0 : 1) <=> (($b['direction'] ?? '') === 'outbound' ? 0 : 1));
                Session::forget('roundtrip_pending');
                Session::set('booking_checkout', [
                    'trip_id' => $tripId,
                    'seat_ids' => $seatIds,
                    'legs' => $legs,
                    'trip_type' => 'roundtrip',
                    'direction' => 'outbound',
                    'booking_group_code' => $bookingMeta['booking_group_code'],
                    'next_trip_id' => 0,
                    'next_direction' => 'outbound',
                    'seats' => $expectedSeats,
                    'return_url' => (string) ($bookingMeta['return_url'] ?? ''),
                ]);
                header('Location: ' . \url('/booking/checkout'));
                return;
            }

            Session::set('roundtrip_pending', $currentLeg);
            Session::flash('success', 'Đã chọn ghế cho một chiều. Vui lòng chọn ghế cho chiều còn lại.');
            header('Location: ' . $this->selectSeatUrl((int) $bookingMeta['next_trip_id'], [
                'trip_type' => 'roundtrip',
                'direction' => (string) ($bookingMeta['next_direction'] ?? ($bookingMeta['direction'] === 'return' ? 'outbound' : 'return')),
                'booking_group_code' => $bookingMeta['booking_group_code'],
                'next_trip_id' => $tripId,
                'next_direction' => $bookingMeta['direction'],
                'seats' => $expectedSeats,
                'return_url' => (string) ($bookingMeta['return_url'] ?? ''),
            ]));
            return;
        }

        Session::set('booking_checkout', [
            'trip_id' => $tripId,
            'seat_ids' => $seatIds,
            'trip_type' => $bookingMeta['trip_type'],
            'direction' => $bookingMeta['direction'],
            'booking_group_code' => $bookingMeta['booking_group_code'],
            'next_trip_id' => $bookingMeta['next_trip_id'],
            'next_direction' => $bookingMeta['next_direction'],
            'seats' => $expectedSeats,
            'return_url' => (string) ($bookingMeta['return_url'] ?? ''),
        ]);

        header('Location: ' . \url('/booking/checkout'));
    }

    private function checkoutRoundTrip(array $legs, array $bookingMeta): void
    {
        $roundTripLegs = [];
        $totalAmount = 0.0;

        foreach ($legs as $leg) {
            $tripId = (int) ($leg['trip_id'] ?? 0);
            $seatIds = $this->seatIdsFromRequest($leg['seat_ids'] ?? []);
            if (count($seatIds) !== $this->seatCountFromMeta($bookingMeta)) {
                Session::flash('error', 'Vui lòng chọn đúng ' . $this->seatCountFromMeta($bookingMeta) . ' ghế cho mỗi chiều.');
                header('Location: ' . \url('/trips/search'));
                return;
            }
            $trip = $tripId > 0 ? (new Trip())->findWithDetails($tripId) : null;
            if ($trip === null || ($trip['status'] ?? '') !== 'scheduled' || $seatIds === []) {
                Session::flash('error', 'Phiên chọn ghế không hợp lệ. Vui lòng chọn lại.');
                header('Location: ' . \url('/trips/search'));
                return;
            }

            $seats = (new Seat())->getAvailableSeatsForTrip($tripId, $seatIds);
            if (count($seats) !== count(array_unique($seatIds))) {
                Session::flash('error', 'Một hoặc nhiều ghế vừa được đặt. Vui lòng chọn lại.');
                header('Location: ' . $this->selectSeatUrl($tripId, $bookingMeta));
                return;
            }

            $legTotal = array_sum(array_map(static fn (array $seat): float => (float) $seat['price'], $seats));
            $totalAmount += $legTotal;
            $roundTripLegs[] = [
                'trip' => $trip,
                'seats' => $seats,
                'direction' => (string) ($leg['direction'] ?? 'outbound'),
                'total_amount' => $legTotal,
            ];
        }

        if (count($roundTripLegs) !== 2) {
            Session::flash('error', 'Vui lòng chọn đủ ghế cho cả 2 chiều.');
            header('Location: ' . \url('/trips/search'));
            return;
        }

        $this->view('bookings.checkout', [
            'title' => 'Xác nhận đặt vé',
            'trip' => $roundTripLegs[0]['trip'],
            'seats' => $roundTripLegs[0]['seats'],
            'roundTripLegs' => $roundTripLegs,
            'totalAmount' => $totalAmount,
            'user' => Auth::user(),
            'bookingMeta' => $bookingMeta,
        ]);
    }

    private function storeRoundTrip(array $legs, array $bookingMeta, string $customerName, string $customerPhone, string $customerEmail, string $paymentMethod): void
    {
        if ($customerName === '' || $customerPhone === '' || $customerEmail === '' || count($legs) !== 2) {
            Session::flash('error', 'Thông tin đặt vé khứ hồi không hợp lệ. Vui lòng điền đầy đủ họ tên, số điện thoại và email.');
            header('Location: ' . \url('/trips/search'));
            return;
        }

        $db = Database::connection();
        $bookingModel = new Booking();
        $detailModel = new BookingDetail();
        $seatModel = new Seat();
        $ticketModel = new Ticket();
        $paymentModel = new Payment();
        $tripModel = new Trip();
        $qrService = new QRCodeService();
        $bookingIds = [];

        try {
            $db->beginTransaction();

            foreach ($legs as $leg) {
                $tripId = (int) ($leg['trip_id'] ?? 0);
                $seatIds = $this->seatIdsFromRequest($leg['seat_ids'] ?? []);
                $direction = ((string) ($leg['direction'] ?? '') === 'return') ? 'return' : 'outbound';
                if (count($seatIds) !== $this->seatCountFromMeta($bookingMeta)) {
                    throw new \RuntimeException('Vui lòng chọn đúng ' . $this->seatCountFromMeta($bookingMeta) . ' ghế cho mỗi chiều.');
                }
                $trip = $tripId > 0 ? $tripModel->findWithDetails($tripId) : null;
                if ($trip === null || ($trip['status'] ?? '') !== 'scheduled' || $seatIds === []) {
                    throw new \RuntimeException('Thông tin chuyến xe hoặc ghế không hợp lệ.');
                }

                $seatModel->lockSeatsForTrip($tripId, $seatIds);
                $seats = $seatModel->getAvailableSeatsForTrip($tripId, $seatIds);
                if (count($seats) !== count(array_unique($seatIds))) {
                    throw new \RuntimeException('Một hoặc nhiều ghế vừa được đặt. Vui lòng chọn lại.');
                }

                if (!$tripModel->decrementAvailableSeats($tripId, count($seats))) {
                    throw new \RuntimeException('Số ghế trống không đủ. Vui lòng chọn lại.');
                }

                $totalAmount = array_sum(array_map(static fn (array $seat): float => (float) $seat['price'], $seats));
                $bookingCode = $bookingModel->generateBookingCode();
                $bookingId = $bookingModel->createBooking([
                    'user_id' => Auth::id(),
                    'trip_id' => $tripId,
                    'booking_code' => $bookingCode,
                    'booking_group_code' => $bookingMeta['booking_group_code'],
                    'trip_type' => 'roundtrip',
                    'direction' => $direction,
                    'customer_name' => $customerName,
                    'customer_phone' => $customerPhone,
                    'customer_email' => $customerEmail !== '' ? $customerEmail : null,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                foreach ($seats as $seat) {
                    $detailModel->createDetail($bookingId, $tripId, (int) $seat['id'], (float) $seat['price']);
                }

                $paymentModel->createPayment([
                    'booking_id' => $bookingId,
                    'method' => $paymentMethod,
                    'amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                $ticketCode = $ticketModel->generateTicketCode();
                $seatNumbers = implode(', ', array_column($seats, 'seat_number'));
                $qrPath = $qrService->generate((string) json_encode($this->ticketQrPayload(
                    $ticketCode,
                    $bookingCode,
                    $bookingId,
                    $trip,
                    [
                        'booking_group_code' => $bookingMeta['booking_group_code'],
                        'trip_type' => 'roundtrip',
                        'direction' => $direction,
                    ],
                    $seatNumbers,
                    $customerName,
                    $customerPhone,
                    $customerEmail !== '' ? $customerEmail : null,
                    $totalAmount,
                    'pending'
                ), JSON_UNESCAPED_UNICODE), $ticketCode);

                $ticketModel->createTicket([
                    'booking_id' => $bookingId,
                    'ticket_code' => $ticketCode,
                    'qr_code_path' => $qrPath,
                    'status' => 'valid',
                ]);

                $bookingIds[] = $bookingId;
            }

            $db->commit();
            Session::forget('booking_checkout');
            
            // Save guest bookings to session
            $guestBookings = Session::get('guest_bookings');
            $guestBookings = is_array($guestBookings) ? $guestBookings : [];
            foreach ($bookingIds as $bId) {
                $guestBookings[] = (int) $bId;
            }
            Session::set('guest_bookings', $guestBookings);

            $this->sendTicketEmails($bookingIds);
            Session::flash('success', 'Đặt vé khứ hồi thành công.');
            header('Location: ' . \url('/booking/detail?id=' . (int) $bookingIds[0]));
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            Session::flash('error', $exception->getMessage());
            header('Location: ' . \url('/trips/search'));
        }
    }

    private function bookingMetaFromRequest(array $source): array
    {
        $tripType = (string) ($source['trip_type'] ?? 'oneway');
        $tripType = $tripType === 'roundtrip' ? 'roundtrip' : 'oneway';

        $direction = (string) ($source['direction'] ?? 'outbound');
        $direction = $direction === 'return' ? 'return' : 'outbound';

        $groupCode = strtoupper(trim((string) ($source['booking_group_code'] ?? '')));
        $groupCode = preg_replace('/[^A-Z0-9_-]/', '', $groupCode) ?: '';

        if ($tripType === 'roundtrip' && $groupCode === '') {
            $groupCode = (new Booking())->generateBookingGroupCode();
        }

        if ($tripType === 'oneway') {
            $groupCode = '';
            $direction = 'outbound';
        }

        $returnUrl = (string) ($source['return_url'] ?? '');
        $returnUrl = str_starts_with($returnUrl, '/') && !str_starts_with($returnUrl, '//') ? $returnUrl : '';

        return [
            'trip_type' => $tripType,
            'direction' => $direction,
            'booking_group_code' => $groupCode !== '' ? $groupCode : null,
            'next_trip_id' => max(0, (int) ($source['next_trip_id'] ?? 0)),
            'next_direction' => ((string) ($source['next_direction'] ?? '') === 'return') ? 'return' : 'outbound',
            'seats' => max(1, min(5, (int) ($source['seats'] ?? 1))),
            'return_url' => $returnUrl,
        ];
    }

    private function ticketQrPayload(
        string $ticketCode,
        string $bookingCode,
        int $bookingId,
        array $trip,
        array $bookingMeta,
        string $seatNumbers,
        string $customerName,
        string $customerPhone,
        ?string $customerEmail,
        float $totalAmount,
        string $bookingStatus
    ): array {
        return [
            'ticket_code' => $ticketCode,
            'booking_id' => $bookingId,
            'booking_code' => $bookingCode,
            'booking_group_code' => $bookingMeta['booking_group_code'] ?? null,
            'trip_type' => (string) ($bookingMeta['trip_type'] ?? 'oneway'),
            'direction' => (string) ($bookingMeta['direction'] ?? 'outbound'),
            'booking_status' => $bookingStatus,
            'customer' => [
                'name' => $customerName,
                'phone' => $customerPhone,
                'email' => $customerEmail,
            ],
            'trip' => [
                'id' => (int) ($trip['id'] ?? 0),
                'from' => (string) ($trip['from_name'] ?? ''),
                'to' => (string) ($trip['to_name'] ?? ''),
                'bus' => (string) ($trip['bus_name'] ?? ''),
                'departure_time' => (string) ($trip['departure_time'] ?? ''),
                'arrival_time' => (string) ($trip['arrival_time'] ?? ''),
            ],
            'seats' => $seatNumbers,
            'total_amount' => $totalAmount,
            'issued_at' => date('c'),
        ];
    }

    private function sendTicketEmails(array $bookingIds): void
    {
        $mailer = new TicketEmailService();
        foreach ($bookingIds as $bookingId) {
            $mailer->sendForBooking((int) $bookingId);
        }
    }

    private function selectSeatUrl(int $tripId, array $bookingMeta): string
    {
        $query = ['trip_id' => $tripId];
        if (($bookingMeta['trip_type'] ?? 'oneway') === 'roundtrip') {
            $query['trip_type'] = 'roundtrip';
            $query['direction'] = (string) ($bookingMeta['direction'] ?? 'outbound');
            $query['booking_group_code'] = (string) ($bookingMeta['booking_group_code'] ?? '');
            if ((int) ($bookingMeta['next_trip_id'] ?? 0) > 0) {
                $query['next_trip_id'] = (int) $bookingMeta['next_trip_id'];
                $query['next_direction'] = (string) ($bookingMeta['next_direction'] ?? '');
            }
        }
        $query['seats'] = $this->seatCountFromMeta($bookingMeta);
        if (!empty($bookingMeta['return_url'])) {
            $query['return_url'] = (string) $bookingMeta['return_url'];
        }

        return \url('/booking/select-seat?' . http_build_query($query));
    }

    private function paymentMethodFromRequest(mixed $method): string
    {
        $method = (string) $method;
        return in_array($method, ['cash', 'bank_transfer', 'momo', 'zalopay', 'card'], true) ? $method : 'cash';
    }

    private function requiresOnlinePayment(string $method): bool
    {
        return in_array($method, ['bank_transfer', 'momo', 'zalopay', 'card'], true);
    }
}
