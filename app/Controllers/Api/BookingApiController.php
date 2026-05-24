<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\Trip;
use App\Services\QRCodeService;
use Throwable;

final class BookingApiController extends Controller
{
    public function create(): void
    {
        if (!Auth::check()) {
            $this->json(['message' => 'Vui lòng đăng nhập để đặt vé.'], 401);
            return;
        }

        $payload = json_decode(file_get_contents('php://input') ?: '[]', true) ?: $_POST;
        $tripId = (int) ($payload['trip_id'] ?? 0);
        $seatIds = $this->seatIds($payload['seat_ids'] ?? $payload['seats'] ?? []);

        $trip = $tripId > 0 ? (new Trip())->findWithDetails($tripId) : null;
        if ($trip === null || ($trip['status'] ?? '') !== 'scheduled' || $seatIds === []) {
            $this->json(['message' => 'Thông tin chuyến xe hoặc ghế không hợp lệ.'], 422);
            return;
        }

        $db = Database::connection();
        try {
            $db->beginTransaction();

            $seatModel = new Seat();
            $seatModel->lockSeatsForTrip($tripId, $seatIds);
            $seats = $seatModel->getAvailableSeatsForTrip($tripId, $seatIds);
            if (count($seats) !== count(array_unique($seatIds))) {
                throw new \RuntimeException('Một hoặc nhiều ghế vừa được đặt. Vui lòng chọn lại.');
            }

            if (!(new Trip())->decrementAvailableSeats($tripId, count($seats))) {
                throw new \RuntimeException('Số ghế trống không đủ. Vui lòng chọn lại.');
            }

            $bookingModel = new Booking();
            $totalAmount = array_sum(array_map(static fn (array $seat): float => (float) $seat['price'], $seats));
            $user = Auth::user() ?? [];
            $bookingId = $bookingModel->createBooking([
                'user_id' => Auth::id(),
                'trip_id' => $tripId,
                'booking_code' => $bookingModel->generateBookingCode(),
                'customer_name' => trim((string) ($payload['customer_name'] ?? $user['name'] ?? 'Khách hàng')),
                'customer_phone' => trim((string) ($payload['customer_phone'] ?? $user['phone'] ?? '0000000000')),
                'customer_email' => trim((string) ($payload['customer_email'] ?? $user['email'] ?? '')) ?: null,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            $detailModel = new BookingDetail();
            foreach ($seats as $seat) {
                $detailModel->createDetail($bookingId, $tripId, (int) $seat['id'], (float) $seat['price']);
            }

            (new Payment())->createPayment([
                'booking_id' => $bookingId,
                'method' => (string) ($payload['payment_method'] ?? 'cash'),
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);

            $ticketModel = new Ticket();
            $ticketCode = $ticketModel->generateTicketCode();
            $qrPayload = json_encode([
                'ticket_code' => $ticketCode,
                'booking_id' => $bookingId,
                'user_id' => Auth::id(),
                'trip_id' => $tripId,
                'seat_number' => implode(', ', array_column($seats, 'seat_number')),
                'status' => 'pending',
            ], JSON_UNESCAPED_UNICODE);
            $qrPath = (new QRCodeService())->generate((string) $qrPayload, $ticketCode);
            $ticketModel->createTicket([
                'booking_id' => $bookingId,
                'ticket_code' => $ticketCode,
                'qr_code_path' => $qrPath,
                'status' => 'valid',
            ]);

            $db->commit();
            $this->json([
                'message' => 'Đặt vé thành công.',
                'booking_id' => $bookingId,
                'redirect' => \url('/booking/detail?id=' . $bookingId),
            ], 201);
        } catch (Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $this->json(['message' => $exception->getMessage()], 409);
        }
    }

    private function seatIds(mixed $input): array
    {
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            $input = is_array($decoded) ? $decoded : explode(',', $input);
        }

        if (!is_array($input)) {
            return [];
        }

        return array_slice(array_values(array_unique(array_filter(array_map('intval', $input)))), 0, 8);
    }
}
