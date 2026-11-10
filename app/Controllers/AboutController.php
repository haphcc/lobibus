<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Services\MailService;
use Throwable;

final class AboutController extends Controller
{
    private MailService $mail;

    public function __construct()
    {
        $this->mail = new MailService();
    }

    public function index(): void
    {
        $trips = [];
        $isLoggedIn = \App\Core\Auth::check();

        if ($isLoggedIn) {
            try {
                $db = \App\Core\Database::connection();
                // Lấy danh sách các chuyến đi mà chính người dùng này đã thực hiện đặt vé thành công
                $stmt = $db->prepare("
                    SELECT DISTINCT t.id AS trip_id, 
                           from_l.name AS from_name, 
                           to_l.name AS to_name, 
                           t.departure_time,
                           b.name AS bus_name
                    FROM bookings bk
                    JOIN trips t ON t.id = bk.trip_id
                    JOIN routes r ON r.id = t.route_id
                    JOIN locations from_l ON from_l.id = r.from_location_id
                    JOIN locations to_l ON to_l.id = r.to_location_id
                    JOIN buses b ON b.id = t.bus_id
                    WHERE bk.user_id = :user_id
                      AND bk.status IN ('confirmed', 'completed')
                    ORDER BY t.departure_time DESC
                ");
                $stmt->execute(['user_id' => \App\Core\Auth::id()]);
                $trips = $stmt->fetchAll();
            } catch (Throwable $e) {
                $trips = [];
            }
        }

        $this->view('about.index', [
            'title' => 'Về chúng tôi',
            'success' => Session::getFlash('success'),
            'error' => Session::getFlash('error'),
            'old' => Session::getFlash('old') ?? [],
            'trips' => $trips,
            'isLoggedIn' => $isLoggedIn,
            'currentUser' => \App\Core\Auth::user(),
        ]);
    }

    public function feedback(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/about');
            return;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        $rating = (int) ($_POST['rating'] ?? 0);
        $tripIdInput = $_POST['trip_id'] ?? '';

        $old = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'message' => $message,
            'rating' => $rating,
            'trip_id' => $tripIdInput,
        ];

        if ($name === '' || $phone === '' || $email === '' || $message === '') {
            Session::flash('error', 'Vui lòng điền đầy đủ tất cả các trường bắt buộc.');
            Session::flash('old', $old);
            $this->redirect('/about');
            return;
        }

        if ($rating < 1 || $rating > 5) {
            Session::flash('error', 'Vui lòng đánh giá số sao từ 1 đến 5 bằng cách nhấp chọn ngôi sao.');
            Session::flash('old', $old);
            $this->redirect('/about');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Địa chỉ email không hợp lệ.');
            Session::flash('old', $old);
            $this->redirect('/about');
            return;
        }

        $userId = null;
        $tripId = null;

        if (\App\Core\Auth::check()) {
            $checkUserId = \App\Core\Auth::id();
            try {
                $db = \App\Core\Database::connection();
                // Xác minh user_id thực sự tồn tại trong bảng users để tránh lỗi khóa ngoại
                $userCheck = $db->prepare("SELECT id FROM users WHERE id = :id");
                $userCheck->execute(['id' => $checkUserId]);
                if ($userCheck->fetch()) {
                    $userId = $checkUserId;
                    
                    $checkTripId = $tripIdInput !== '' ? (int) $tripIdInput : null;
                    if ($checkTripId !== null) {
                        // Xác minh trip_id thực sự tồn tại trong bảng trips để tránh lỗi khóa ngoại
                        $tripCheck = $db->prepare("SELECT id FROM trips WHERE id = :id");
                        $tripCheck->execute(['id' => $checkTripId]);
                        if ($tripCheck->fetch()) {
                            $tripId = $checkTripId;
                        }
                    }
                }
            } catch (Throwable $e) {
                // Nếu có bất kỳ lỗi kết nối/truy vấn nào, giữ mặc định là null
                $userId = null;
                $tripId = null;
            }
        }

        // 1. Lưu đánh giá vào Database
        try {
            $db = \App\Core\Database::connection();
            $stmt = $db->prepare("
                INSERT INTO reviews (user_id, trip_id, rating, comment) 
                VALUES (:user_id, :trip_id, :rating, :comment)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'trip_id' => $tripId,
                'rating' => $rating,
                'comment' => $message,
            ]);
        } catch (Throwable $dbEx) {
            Session::flash('error', 'Không thể lưu đánh giá vào cơ sở dữ liệu: ' . $dbEx->getMessage());
            Session::flash('old', $old);
            $this->redirect('/about');
            return;
        }

        // Lấy thông tin chuyến đi đã chọn (nếu có) để nhúng vào email
        $tripDetails = '';
        if ($tripId !== null) {
            try {
                $tripQuery = $db->prepare("
                    SELECT from_l.name AS from_name, to_l.name AS to_name, t.departure_time, b.name AS bus_name
                    FROM trips t
                    JOIN routes r ON r.id = t.route_id
                    JOIN locations from_l ON from_l.id = r.from_location_id
                    JOIN locations to_l ON to_l.id = r.to_location_id
                    JOIN buses b ON b.id = t.bus_id
                    WHERE t.id = :trip_id
                ");
                $tripQuery->execute(['trip_id' => $tripId]);
                $t = $tripQuery->fetch();
                if ($t) {
                    $formattedTime = date('H:i d/m/Y', strtotime((string) $t['departure_time']));
                    $tripDetails = "{$t['from_name']} -> {$t['to_name']} ({$t['bus_name']} - Khởi hành: {$formattedTime})";
                }
            } catch (Throwable $e) {
                // Bỏ qua lỗi lấy chi tiết chuyến xe trong email
                $tripDetails = '';
            }
        }

        // 2. Gửi email xác nhận về cho người dùng
        try {
            $subject = 'Cảm ơn bạn đã gửi đánh giá đến LobiBus';
            $body = $this->feedbackEmailBody($name, $phone, $email, $message, $rating, $tripDetails);
            $sent = $this->mail->send($email, $subject, $body);

            if ($sent) {
                Session::flash('success', 'Cảm ơn bạn! Đánh giá & góp ý đã được ghi nhận thành công. Chúng tôi đã gửi một email xác nhận đến hòm thư của bạn.');
            } else {
                Session::flash('success', 'Đánh giá đã được lưu vào hệ thống, nhưng có lỗi xảy ra khi gửi email xác nhận.');
            }
        } catch (Throwable $e) {
            Session::flash('success', 'Đánh giá đã được lưu vào hệ thống. Lỗi gửi mail: ' . $e->getMessage());
        }

        $this->redirect('/about');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . \url($path));
        exit;
    }

    private function feedbackEmailBody(string $name, string $phone, string $email, string $message, int $rating, string $tripDetails): string
    {
        $displayName = \e($name);
        $safePhone = \e($phone);
        $safeEmail = \e($email);
        $safeMessage = nl2br(\e($message));
        $starsHtml = str_repeat('⭐', $rating);
        $year = date('Y');

        $tripRowHtml = '';
        if ($tripDetails !== '') {
            $tripRowHtml = '<div style="margin-bottom:8px;"><strong>Chuyến xe đánh giá:</strong> ' . \e($tripDetails) . '</div>';
        }

        return <<<HTML
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Cảm ơn bạn đã gửi đánh giá đến LobiBus</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f6;font-family:Arial,Helvetica,sans-serif;color:#18352d;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7f6;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border:1px solid #dcebe4;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f766e;padding:22px 28px;color:#ffffff;">
                            <div style="font-size:22px;font-weight:800;letter-spacing:.2px;">LobiBus</div>
                            <div style="font-size:14px;opacity:.9;margin-top:4px;">Gửi đánh giá & góp ý thành công</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="font-size:16px;line-height:1.6;margin:0 0 16px;">Xin chào {$displayName},</p>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 18px;">
                                Cảm ơn bạn đã gửi ý kiến đóng góp và đánh giá quý giá cho LobiBus. Hệ thống của chúng tôi đã ghi nhận thành công đánh giá của bạn.
                            </p>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 10px;font-weight:bold;color:#0f766e;">
                                Chi tiết đánh giá ghi nhận:
                            </p>
                            <div style="background:#ecfdf5;border:1px solid #b9e6d1;border-radius:8px;padding:18px;margin:15px 0;line-height:1.6;font-size:14px;">
                                <div style="margin-bottom:8px;"><strong>Họ và tên:</strong> {$displayName}</div>
                                <div style="margin-bottom:8px;"><strong>Số điện thoại:</strong> {$safePhone}</div>
                                <div style="margin-bottom:8px;"><strong>Email liên hệ:</strong> {$safeEmail}</div>
                                <div style="margin-bottom:8px;"><strong>Mức độ hài lòng:</strong> <span style="font-size:16px;">{$starsHtml}</span> ({$rating}/5 sao)</div>
                                {$tripRowHtml}
                                <div><strong>Nội dung nhận xét:</strong></div>
                                <div style="margin-top:6px;background:#ffffff;border:1px solid #dcebe4;padding:12px;border-radius:6px;font-style:italic;color:#334155;">
                                    {$safeMessage}
                                </div>
                            </div>
                            <p style="font-size:15px;line-height:1.7;margin:0 0 18px;">
                                Ý kiến của bạn sẽ được chuyển đến ban quản lý LobiBus để kiểm tra và nâng cao chất lượng dịch vụ. Chúng tôi luôn trân trọng mọi phản hồi để cải thiện dịch vụ xe khách ngày một tốt hơn.
                            </p>
                            <p style="font-size:13px;line-height:1.6;color:#6b7f77;margin:0;">
                                Nếu bạn không thực hiện gửi góp ý này, vui lòng bỏ qua email hoặc liên hệ bộ phận hỗ trợ khách hàng của LobiBus để được hỗ trợ.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f7fbf9;border-top:1px solid #e4efea;padding:16px 28px;color:#6b7f77;font-size:12px;line-height:1.5;">
                            Email này được gửi tự động từ hệ thống LobiBus. Vui lòng không trả lời trực tiếp email này.<br>
                            &copy; {$year} LobiBus. Đã đăng ký bản quyền.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
