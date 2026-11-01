<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Chatbot;
use App\Core\Database;
use App\Core\Session;

final class ChatbotService
{
    /**
     * Hủy/làm sạch lịch sử trò chuyện nếu cần
     */
    public function clearHistory(): void
    {
        Session::forget('ai_chat_history');
    }

    /**
     * Xử lý câu trả lời từ khách hàng bằng Hybrid AI (OpenAI GPT-4o-mini + Local DB Fallback)
     */
    public function reply(string $message): string
    {
        if (trim($message) === '') {
            return 'Vui lòng nhập câu hỏi để LobiBus có thể hỗ trợ bạn.';
        }

        // Khởi tạo Session nếu chưa chạy (App.php đã chạy, nhưng gọi thêm cho chắc chắn)
        Session::start();

        // Lấy OpenAI API Key từ biến môi trường
        $apiKey = getenv('OPENAI_API_KEY');
        if (empty($apiKey) || str_starts_with($apiKey, 'sk-placeholder')) {
            // Không có API Key -> Chuyển sang công cụ cục bộ siêu thông minh (Local Regex + DB matching)
            return $this->localSmartReply($message);
        }

        // Lấy lịch sử trò chuyện từ Session
        $history = Session::get('ai_chat_history');
        if (!$history || !is_array($history)) {
            $history = [
                ['role' => 'system', 'content' => $this->getSystemPrompt()]
            ];
        } else {
            // Đảm bảo System Prompt luôn cập nhật ở vị trí đầu tiên
            $history[0] = ['role' => 'system', 'content' => $this->getSystemPrompt()];
        }

        // Giới hạn chiều dài lịch sử (tối đa 15 tin nhắn) để tránh tràn token
        if (count($history) > 15) {
            $sysPrompt = $history[0];
            $recent = array_slice($history, -8);
            $history = array_merge([$sysPrompt], $recent);
        }

        // Thêm câu hỏi của user vào lịch sử
        $history[] = ['role' => 'user', 'content' => $message];
        Session::set('ai_chat_history', $history);

        try {
            // Bước 1: Gửi yêu cầu lên OpenAI GPT-4o-mini để nhận diện Intent/Tool
            $openaiResponse = $this->callOpenAI($history);
            if (!$openaiResponse) {
                return $this->localSmartReply($message);
            }

            $choice = $openaiResponse['choices'][0]['message'] ?? null;
            if (!$choice) {
                return $this->localSmartReply($message);
            }

            $content = $choice['content'] ?? '';
            $parsed = json_decode($content, true);

            if (!$parsed) {
                // Nếu LLM không trả về JSON hợp lệ, thử dùng local fallback
                return $this->localSmartReply($message);
            }

            $tool = $parsed['tool'] ?? 'none';
            $arguments = $parsed['arguments'] ?? [];

            if ($tool !== 'none') {
                // Bước 2: LLM yêu cầu gọi công cụ -> Thực thi truy vấn CSDL an toàn
                $dbResult = $this->executeTool($tool, $arguments);

                // Lưu suy nghĩ của LLM và kết quả CSDL vào lịch sử trò chuyện
                $history[] = ['role' => 'assistant', 'content' => $content];
                $history[] = [
                    'role' => 'system', 
                    'content' => "[System Database Output for tool \"$tool\"]: " . json_encode($dbResult, JSON_UNESCAPED_UNICODE)
                ];
                Session::set('ai_chat_history', $history);

                // Bước 3: Gọi OpenAI lần thứ 2 để tổng hợp kết quả CSDL thành câu trả lời tự nhiên
                $secondResponse = $this->callOpenAI($history);
                if (!$secondResponse) {
                    return $this->localSmartReply($message);
                }

                $secondChoice = $secondResponse['choices'][0]['message'] ?? null;
                if (!$secondChoice) {
                    return $this->localSmartReply($message);
                }

                $secondContent = $secondChoice['content'] ?? '';
                $secondParsed = json_decode($secondContent, true);

                if ($secondParsed && isset($secondParsed['reply'])) {
                    $finalReply = $secondParsed['reply'];
                    $history[] = ['role' => 'assistant', 'content' => $secondContent];
                    Session::set('ai_chat_history', $history);
                    return $finalReply;
                } else {
                    return $secondContent;
                }
            } else {
                // Không cần gọi Tool (ví dụ: chào hỏi hoặc FAQ thông thường)
                $finalReply = $parsed['reply'] ?? 'LobiBus chưa có câu trả lời phù hợp.';
                $history[] = ['role' => 'assistant', 'content' => $content];
                Session::set('ai_chat_history', $history);
                return $finalReply;
            }

        } catch (\Throwable $e) {
            // Gặp lỗi kết nối/API -> Tự động kích hoạt Local Smart Fallback không lo sập
            return $this->localSmartReply($message);
        }
    }

    /**
     * Gọi API OpenAI Chat Completion
     */
    private function callOpenAI(array $messages): ?array
    {
        $apiKey = getenv('OPENAI_API_KEY');
        if (empty($apiKey)) {
            return null;
        }

        $url = 'https://api.openai.com/v1/chat/completions';
        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.2
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tránh lỗi SSL cục bộ trên XAMPP Windows

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        return json_decode((string)$response, true);
    }

    /**
     * Thực thi các tool truy vấn DB một cách an toàn
     */
    private function executeTool(string $tool, array $arguments): array
    {
        switch ($tool) {
            case 'search_trips':
                $from = (string)($arguments['from'] ?? '');
                $to = (string)($arguments['to'] ?? '');
                $date = (string)($arguments['date'] ?? null);
                return $this->dbSearchTrips($from, $to, $date);

            case 'get_booking_status':
                $code = (string)($arguments['code'] ?? '');
                return $this->dbGetBookingStatus($code);

            case 'list_routes':
                return $this->dbListRoutes();

            case 'get_bus_and_seats':
                $tripId = (int)($arguments['trip_id'] ?? 0);
                return $this->dbGetTripSeats($tripId);

            case 'get_reviews':
                return $this->dbGetReviews();

            case 'faq_lookup':
                $query = (string)($arguments['query'] ?? '');
                $model = new Chatbot();
                $answer = $model->findAnswer($query);
                return ['found' => $answer !== null, 'answer' => $answer];

            default:
                return ['error' => "Unknown tool: $tool"];
        }
    }

    /**
     * System Prompt hướng dẫn LLM hoạt động theo cấu trúc JSON định sẵn
     */
    private function getSystemPrompt(): string
    {
        return "Bạn là LobiBus Assistant - Trợ lý ảo thông minh của hệ thống đặt vé xe khách LobiBus.
Nhiệm vụ của bạn là hỗ trợ khách hàng trả lời chính xác các câu hỏi bằng cách tra cứu cơ sở dữ liệu (CSDL) trực tiếp của hệ thống.

QUY TẮC CỰC KỲ QUAN TRỌNG:
1. Bạn KHÔNG ĐƯỢC tự bịa ra thông tin (hallucinate) VỀ CÁC THÔNG TIN ĐẶT VÉ, MÃ VÉ, GIỜ CHẠY HOẶC GIÁ VÉ THỰC TẾ TRÊN HỆ THỐNG LOBIBUS. Không tự tạo mã đặt vé giả lập hay chuyến xe giả lập nếu không có trong cơ sở dữ liệu.
2. Nếu câu hỏi yêu cầu dữ liệu thực tế trên hệ thống (chuyến xe, đặt vé, tuyến đường, đánh giá), bạn phải gọi công cụ (tool) tương ứng để tra cứu CSDL trước.
3. ĐẶC BIỆT: Nếu thông tin khách hàng hỏi nằm ngoài phạm vi khai thác của hệ thống LobiBus (ví dụ: khoảng cách địa lý ngoài đời từ TP.HCM đến Hà Nội, hoặc câu hỏi kiến thức phổ thông, địa lý, lịch sử...), bạn hãy giải thích rõ là 'Hệ thống đặt vé LobiBus hiện tại chưa khai thác tuyến đường/thông tin này', sau đó bạn CÓ THỂ sử dụng kiến thức nền tảng (pre-trained knowledge) của chính bạn để trả lời chi tiết và đầy đủ cho khách hàng (ví dụ: cung cấp khoảng cách địa lý thực tế là khoảng 1.720 km đường bộ, thời gian di chuyển bằng máy bay/tàu hỏa...). Đừng từ chối trả lời nếu bạn đã biết câu trả lời thực tế ngoài đời!
4. Luôn luôn trả lời bằng định dạng JSON sau:
{
  \"thought\": \"Suy nghĩ của bạn bằng tiếng Việt về câu hỏi của khách hàng và bước tiếp theo cần làm.\",
  \"tool\": \"tên_công_cụ_hoặc_none\",
  \"arguments\": {
    \"tên_tham_số\": \"giá_trị\"
  },
  \"reply\": \"Câu trả lời cuối cùng bằng tiếng Việt gửi cho khách hàng (chỉ điền khi tool là 'none').\"
}

CÁC CÔNG CỤ (TOOLS) BẠN CÓ THỂ GỌI (Điền vào trường \"tool\"):
- \"search_trips\": Tìm kiếm chuyến xe từ nơi đi đến nơi đến.
  Tham số: {\"from\": \"tên nơi đi\", \"to\": \"tên nơi đến\", \"date\": \"YYYY-MM-DD\" (nếu có)}
- \"get_booking_status\": Tra cứu tình trạng đặt vé, vé xe, thanh toán.
  Tham số: {\"code\": \"mã đặt vé hoặc số điện thoại hoặc mã vé hoặc email\"}
- \"list_routes\": Xem danh sách tất cả các tuyến đường xe chạy hiện hành.
  Tham số: {}
- \"get_bus_and_seats\": Tra cứu sơ đồ ghế và tình trạng ghế trống của một chuyến xe.
  Tham số: {\"trip_id\": ID_chuyến_xe_kiểu_số}
- \"get_reviews\": Xem đánh giá và bình luận gần đây của khách hàng về các chuyến đi.
  Tham số: {}
- \"faq_lookup\": Tra cứu các câu hỏi thường gặp (bảng chatbot_questions).
  Tham số: {\"query\": \"từ khóa cần tìm\"}

Kịch bản hoạt động:
Bước 1: Nhận câu hỏi từ khách hàng. Phân tích xem có cần truy vấn CSDL hay không. Nếu có, hãy điền tên công cụ tương ứng vào trường \"tool\" và các tham số vào \"arguments\". Trường \"reply\" để trống.
Bước 2: Hệ thống sẽ thực thi câu lệnh SQL an toàn tương ứng và trả lại dữ liệu thực tế cho bạn dưới dạng tin nhắn hệ thống [System Database Output].
Bước 3: Bạn phân tích dữ liệu thực tế nhận được từ hệ thống, đặt \"tool\": \"none\" và viết câu trả lời hoàn chỉnh, thân thiện, rõ ràng bằng tiếng Việt ở trường \"reply\". Sử dụng Markdown đẹp mắt.";
    }

    // =========================================================================
    // HỆ THỐNG TRUY VẤN CƠ SỞ DỮ LIỆU AN TOÀN (SAFE DATABASE API RUNNERS)
    // =========================================================================

    private function findLocationId(string $name): ?int
    {
        $db = Database::connection();
        $stmt = $db->prepare('SELECT id FROM locations WHERE name LIKE :name OR province LIKE :province LIMIT 1');
        $stmt->execute([
            ':name' => '%' . $name . '%',
            ':province' => '%' . $name . '%'
        ]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }

    public function dbSearchTrips(string $from, string $to, ?string $date = null): array
    {
        $db = Database::connection();
        $fromId = $this->findLocationId($from);
        $toId = $this->findLocationId($to);
        if (!$fromId || !$toId) {
            return [];
        }

        $query = 'SELECT t.id, r.distance_km, r.duration_minutes, t.departure_time, t.arrival_time, t.price, t.status, b.name as bus_name, b.bus_type, b.total_seats,
                         (SELECT COUNT(*) FROM booking_details bd JOIN bookings bk ON bd.booking_id = bk.id WHERE bd.trip_id = t.id AND bk.status NOT IN ("cancelled", "expired")) as booked_seats
                  FROM trips t
                  JOIN routes r ON t.route_id = r.id
                  JOIN buses b ON t.bus_id = b.id
                  WHERE r.from_location_id = :from_id AND r.to_location_id = :to_id';

        $params = [
            ':from_id' => $fromId,
            ':to_id' => $toId
        ];

        if ($date) {
            $query .= ' AND DATE(t.departure_time) = :date';
            $params[':date'] = $date;
        }

        $query .= ' ORDER BY t.departure_time ASC LIMIT 10';
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function dbGetBookingStatus(string $code): array
    {
        $db = Database::connection();
        // Kiểm tra xem là mã đặt vé (LB-...) hay mã vé (TICK-...) hay SĐT
        $stmt = $db->prepare('
            SELECT b.id as booking_id, b.booking_code, b.customer_name, b.customer_phone, b.customer_email, b.total_amount, b.status as booking_status, b.created_at,
                   t.id as trip_id, t.departure_time, t.arrival_time, t.price as trip_price,
                   r.distance_km, r.duration_minutes,
                   lf.name as from_loc, lt.name as to_loc,
                   bus.name as bus_name, bus.bus_type,
                   pay.method as payment_method, pay.amount as payment_amount, pay.status as payment_status, pay.transaction_code,
                   tk.ticket_code, tk.status as ticket_status
            FROM bookings b
            JOIN trips t ON b.trip_id = t.id
            JOIN routes r ON t.route_id = r.id
            JOIN locations lf ON r.from_location_id = lf.id
            JOIN locations lt ON r.to_location_id = lt.id
            JOIN buses bus ON t.bus_id = bus.id
            LEFT JOIN payments pay ON pay.booking_id = b.id
            LEFT JOIN tickets tk ON tk.booking_id = b.id
            WHERE b.booking_code = :code 
               OR b.customer_phone = :phone 
               OR b.customer_email = :email
               OR tk.ticket_code = :ticket_code
            ORDER BY b.id DESC
            LIMIT 5
        ');
        $stmt->execute([
            ':code' => $code,
            ':phone' => $code,
            ':email' => $code,
            ':ticket_code' => $code
        ]);
        $rows = $stmt->fetchAll();

        // Bổ sung danh sách số ghế đã đặt cho mỗi booking để phản hồi đầy đủ
        foreach ($rows as &$row) {
            $stmtSeats = $db->prepare('
                SELECT s.seat_number 
                FROM booking_details bd
                JOIN seats s ON bd.seat_id = s.id
                WHERE bd.booking_id = :booking_id
            ');
            $stmtSeats->execute([':booking_id' => $row['booking_id']]);
            $row['seats'] = array_column($stmtSeats->fetchAll(), 'seat_number');
        }

        return $rows;
    }

    public function dbListRoutes(): array
    {
        $db = Database::connection();
        $stmt = $db->query('
            SELECT r.id, lf.name as from_loc, lt.name as to_loc, r.distance_km, r.duration_minutes, r.status
            FROM routes r
            JOIN locations lf ON r.from_location_id = lf.id
            JOIN locations lt ON r.to_location_id = lt.id
            WHERE r.status = "active"
        ');
        return $stmt->fetchAll();
    }

    public function dbGetTripSeats(int $tripId): array
    {
        $db = Database::connection();
        $stmt = $db->prepare('
            SELECT t.id, b.name as bus_name, b.total_seats
            FROM trips t
            JOIN buses b ON t.bus_id = b.id
            WHERE t.id = :trip_id
        ');
        $stmt->execute([':trip_id' => $tripId]);
        $trip = $stmt->fetch();
        if (!$trip) return [];

        $stmt2 = $db->prepare('
            SELECT s.seat_number
            FROM booking_details bd
            JOIN bookings bk ON bd.booking_id = bk.id
            JOIN seats s ON bd.seat_id = s.id
            WHERE bd.trip_id = :trip_id AND bk.status NOT IN ("cancelled", "expired")
        ');
        $stmt2->execute([':trip_id' => $tripId]);
        $bookedSeats = array_column($stmt2->fetchAll(), 'seat_number');

        return [
            'trip_id' => $tripId,
            'bus_name' => $trip['bus_name'],
            'total_seats' => $trip['total_seats'],
            'booked_count' => count($bookedSeats),
            'available_count' => $trip['total_seats'] - count($bookedSeats),
            'booked_seats' => $bookedSeats
        ];
    }

    public function dbGetReviews(): array
    {
        $db = Database::connection();
        $stmt = $db->query('
            SELECT rv.rating, rv.comment, rv.created_at, u.name as customer_name,
                   lf.name as from_loc, lt.name as to_loc
             FROM reviews rv
             LEFT JOIN users u ON rv.user_id = u.id
             JOIN trips t ON rv.trip_id = t.id
             JOIN routes r ON t.route_id = r.id
             JOIN locations lf ON r.from_location_id = lf.id
             JOIN locations lt ON r.to_location_id = lt.id
             ORDER BY rv.id DESC
             LIMIT 5
        ');
        return $stmt->fetchAll();
    }

    private function getAllLocationNames(): array
    {
        $db = Database::connection();
        $stmt = $db->query('SELECT name, province FROM locations');
        $rows = $stmt->fetchAll();
        $names = [];
        foreach ($rows as $row) {
            if ($row['name']) $names[] = $row['name'];
            if ($row['province'] && !in_array($row['province'], $names)) $names[] = $row['province'];
        }
        return array_values(array_unique($names));
    }

    // =========================================================================
    // LOCAL SMART FALLBACK (ALGORITHM WITHOUT API KEY)
    // =========================================================================

    public function localSmartReply(string $message): string
    {
        $text = mb_strtolower(trim($message));

        // 1. Phân tích mã đặt vé hoặc mã vé hoặc số điện thoại
        if (preg_match('/(lb-\d{8}-\d{4}|tick-\d{8}-\d{4})/i', $text, $matches)) {
            $code = strtoupper($matches[1]);
            $bookings = $this->dbGetBookingStatus($code);
            if (!empty($bookings)) {
                return $this->formatBookingStatusResponse($bookings);
            }
        }

        // Kiểm tra xem có chứa số điện thoại không (9-11 chữ số)
        if (preg_match('/(0[35789]\d{8})/', $text, $matches)) {
            $phone = $matches[1];
            $bookings = $this->dbGetBookingStatus($phone);
            if (!empty($bookings)) {
                return $this->formatBookingStatusResponse($bookings);
            }
        }

        // 2. Tìm kiếm các địa danh trong câu hỏi để tra cứu chuyến xe
        $locations = $this->getAllLocationNames();
        $foundLocations = [];
        foreach ($locations as $loc) {
            $normalizedLoc = mb_strtolower($loc);
            // Tránh nhầm lẫn địa danh quá ngắn
            if (mb_strlen($normalizedLoc) >= 3 && str_contains($text, $normalizedLoc)) {
                $foundLocations[] = $loc;
            }
        }

        // Trích xuất chuyến xe nếu phát hiện ít nhất 2 địa điểm
        if (count($foundLocations) >= 2) {
            $pos0 = mb_strpos($text, mb_strtolower($foundLocations[0]));
            $pos1 = mb_strpos($text, mb_strtolower($foundLocations[1]));
            if ($pos0 < $pos1) {
                $from = $foundLocations[0];
                $to = $foundLocations[1];
            } else {
                $from = $foundLocations[1];
                $to = $foundLocations[0];
            }

            // Tìm ngày dạng YYYY-MM-DD hoặc DD-MM-YYYY hoặc DD/MM/YYYY
            $date = null;
            if (preg_match('/(\d{4}-\d{2}-\d{2})/', $text, $dateMatches)) {
                $date = $dateMatches[1];
            } elseif (preg_match('/(\d{2})[-|\/](\d{2})[-|\/](\d{4})/', $text, $dateMatches)) {
                $date = $dateMatches[3] . '-' . $dateMatches[2] . '-' . $dateMatches[1];
            }

            $trips = $this->dbSearchTrips($from, $to, $date);
            if (!empty($trips)) {
                return $this->formatTripsResponse($from, $to, $trips);
            } else {
                // Nếu khách hỏi về khoảng cách địa lý ngoài đời mà tuyến đó không có trong DB
                if (preg_match('/(khoảng cách|khoang cach|độ dài|do dai|bao nhiêu km|bao nhieu km|bao xa)/i', $text)) {
                    $fromLower = mb_strtolower($from);
                    $toLower = mb_strtolower($to);
                    if ((str_contains($fromLower, 'hồ chí minh') || str_contains($fromLower, 'tp.hcm') || str_contains($fromLower, 'sài gòn') || str_contains($fromLower, 'hcm'))
                        && (str_contains($toLower, 'hà nội') || str_contains($toLower, 'ha noi'))) {
                        return "Tuyến đường từ **$from** đi **$to** hiện tại LobiBus chưa đưa vào khai thác trên hệ thống đặt vé.\n\n💡 Tuy nhiên, theo thông tin địa lý thực tế, khoảng cách đường bộ giữa TP.HCM và Hà Nội là khoảng **1.720 km** (dọc theo Quốc lộ 1A) và khoảng **1.160 km** đường hàng không. Thời gian di chuyển đường bộ bằng xe khách thường mất tầm 30 - 36 tiếng, trong khi bay thẳng chỉ mất khoảng 2 tiếng.";
                    }
                    if ((str_contains($toLower, 'hồ chí minh') || str_contains($toLower, 'tp.hcm') || str_contains($toLower, 'sài gòn') || str_contains($toLower, 'hcm'))
                        && (str_contains($fromLower, 'hà nội') || str_contains($fromLower, 'ha noi'))) {
                        return "Tuyến đường từ **$from** đi **$to** hiện tại LobiBus chưa đưa vào khai thác trên hệ thống đặt vé.\n\n💡 Tuy nhiên, theo thông tin địa lý thực tế, khoảng cách đường bộ giữa Hà Nội và TP.HCM là khoảng **1.720 km** (dọc theo Quốc lộ 1A) và khoảng **1.160 km** đường hàng không. Thời gian di chuyển đường bộ bằng xe khách thường mất tầm 30 - 36 tiếng, trong khi bay thẳng chỉ mất khoảng 2 tiếng.";
                    }
                    // Các cặp địa điểm khác
                    return "Tuyến đường từ **$from** đi **$to** hiện tại LobiBus chưa đưa vào khai thác trên hệ thống đặt vé nên chưa có dữ liệu cự ly trong hệ thống.\n\n💡 Bạn có thể hỏi về các tuyến đang hoạt động như: *\"Hà Nội đi Hải Phòng bao xa?\"* hoặc *\"TP.HCM đi Cần Thơ bao nhiêu km?\"* nhé!";
                }

                $formattedDate = $date ? " vào ngày **" . date('d/m/Y', strtotime($date)) . "**" : "";
                return "LobiBus hiện tại không tìm thấy chuyến xe nào hoạt động từ **$from** đi **$to**$formattedDate.\n\n" .
                       "💡 Bạn vui lòng chọn ngày khởi hành khác hoặc kiểm tra lại tên tuyến đường nhé!";
            }
        }

        // 3. Tra cứu tuyến đường, lộ trình, giá vé
        if (str_contains($text, 'tuyến') || str_contains($text, 'tuyen') || str_contains($text, 'lộ trình') || str_contains($text, 'lo trinh') || str_contains($text, 'giá vé') || str_contains($text, 'gia ve')) {
            if (!str_contains($text, 'đánh giá') && !str_contains($text, 'danh gia')) {
                $routes = $this->dbListRoutes();
                if (!empty($routes)) {
                    $reply = "📍 **Các tuyến đường cao tốc đang khai thác tại LobiBus:**\n\n";
                    foreach ($routes as $r) {
                        $reply .= "- **{$r['from_loc']} ⇄ {$r['to_loc']}**: Cự ly khoảng **{$r['distance_km']} km** (Thời gian chạy tầm ~**{$r['duration_minutes']} phút**)\n";
                    }
                    $reply .= "\n👉 Để xem giờ khởi hành và giá vé thực tế, bạn hãy nhắn theo mẫu: *\"tuyến Hà Nội đi Hải Phòng\"* hoặc *\"chuyến đi Hà Nội đến Hải Phòng ngày mai\"*.";
                    return $reply;
                }
            }
        }

        // 4. Tra cứu đánh giá, nhận xét khách hàng
        if (str_contains($text, 'đánh giá') || str_contains($text, 'danh gia') || str_contains($text, 'nhận xét') || str_contains($text, 'nhan xet') || str_contains($text, 'phản hồi') || str_contains($text, 'phan hoi') || str_contains($text, 'review')) {
            $reviews = $this->dbGetReviews();
            if (!empty($reviews)) {
                $reply = "⭐ **Đánh giá & Phản hồi thực tế từ khách hàng đi xe LobiBus:**\n\n";
                foreach ($reviews as $rev) {
                    $stars = str_repeat('⭐', (int)$rev['rating']);
                    $reply .= "- **{$rev['customer_name']}** ({$rev['from_loc']} ⇄ {$rev['to_loc']}): $stars\n  *\"{$rev['comment']}\"*\n\n";
                }
                return $reply;
            }
        }

        // 5. Tìm kiếm trong bảng chatbot_questions (FAQ) theo keyword
        $modelChatbot = new Chatbot();
        $answer = $modelChatbot->findAnswer($message);
        if ($answer !== null) {
            return $answer;
        }

        // 6. Phản hồi gợi ý thông minh mặc định khi không khớp từ khóa
        return "👋 Xin chào! Mình là **LobiBus Assistant**.\n\n" .
               "Hiện tại mình đã được đồng bộ trực tiếp với Cơ sở dữ liệu của LobiBus. Bạn có thể hỏi mình các thông tin thực tế sau:\n\n" .
               "1. **Tra cứu Chuyến xe**: Nhập tuyến đi và đến (ví dụ: *\"Có chuyến nào từ Hà Nội đi Hải Phòng không?\"*)\n" .
               "2. **Tra cứu Vé đã đặt**: Nhập mã đặt vé hoặc số điện thoại (ví dụ: *\"Tra cứu vé LB-20260520-0001\"* hoặc *\"Kiểm tra số điện thoại 0911000001\"*)\n" .
               "3. **Xem Đánh giá**: Gõ *\"đánh giá gần đây\"* hoặc *\"khách hàng nói gì về LobiBus\"*\n" .
               "4. **Hỗ trợ chung**: Hỏi về *hủy vé*, *hoàn tiền*, *thanh toán*, *hành lý*, *trễ giờ*...";
    }

    private function formatBookingStatusResponse(array $bookings): string
    {
        $reply = "🔍 Tìm thấy **" . count($bookings) . "** đơn đặt vé khớp với thông tin của bạn trên hệ thống:\n\n";
        foreach ($bookings as $b) {
            $payStatus = $b['payment_status'] ?? 'pending';
            $payStatusText = $payStatus === 'paid' ? 'Đã thanh toán ✅' : ($payStatus === 'pending' ? 'Chờ thanh toán ⏳' : 'Thất bại/Hủy bỏ ❌');
            
            $bookingStatus = $b['booking_status'];
            $bookingStatusText = match($bookingStatus) {
                'confirmed' => 'Đã xác nhận (Thành công) 🟢',
                'pending' => 'Chờ xác nhận 🟡',
                'cancelled' => 'Đã hủy 🔴',
                'completed' => 'Đã hoàn thành chuyến đi 🔵',
                'expired' => 'Đã hết hạn giữ chỗ ⚫',
                default => $bookingStatus
            };

            $seatsText = !empty($b['seats']) ? implode(', ', $b['seats']) : 'Chưa xếp ghế';

            $reply .= "### 🎟️ Mã đặt vé: **{$b['booking_code']}**\n";
            $reply .= "- **Hành khách**: **{$b['customer_name']}** ({$b['customer_phone']})\n";
            $reply .= "- **Hành trình**: **{$b['from_loc']} ⇄ {$b['to_loc']}**\n";
            $reply .= "- **Giờ khởi hành**: " . date('H:i d/m/Y', strtotime($b['departure_time'])) . "\n";
            $reply .= "- **Xe phục vụ**: {$b['bus_name']} (Loại: " . ucfirst($b['bus_type']) . ")\n";
            $reply .= "- **Vị trí ghế**: Danh sách ghế **[$seatsText]**\n";
            $reply .= "- **Mã vé lên xe (Ticket)**: `" . ($b['ticket_code'] ?? 'Chưa phát hành') . "` (Trạng thái vé: " . ($b['ticket_status'] ?? 'Chưa xác định') . ")\n";
            $reply .= "- **Tổng tiền thanh toán**: **" . number_format((float)$b['total_amount'], 0, ',', '.') . " VNĐ**\n";
            $reply .= "- **Trạng thái đơn hàng**: **$bookingStatusText**\n";
            $reply .= "- **Trạng thái thanh toán**: **$payStatusText** (Cổng: " . ($b['payment_method'] ? strtoupper($b['payment_method']) : 'Chưa chọn') . ")\n\n";
            $reply .= "--- \n\n";
        }
        return $reply;
    }

    private function formatTripsResponse(string $from, string $to, array $trips): string
    {
        $reply = "🚌 **Các chuyến xe hoạt động trên tuyến từ $from đi $to được tìm thấy:**\n\n";
        foreach ($trips as $t) {
            $depTime = date('H:i d/m/Y', strtotime($t['departure_time']));
            $arrTime = date('H:i d/m/Y', strtotime($t['arrival_time']));
            $price = number_format((float)$t['price'], 0, ',', '.') . " VNĐ";
            $availableSeats = (int)$t['total_seats'] - (int)$t['booked_seats'];

            $reply .= "### Chuyến đi #{$t['id']}: Xe **{$t['bus_name']}**\n";
            $reply .= "- 🕒 **Khởi hành**: `$depTime` ⇄ **Dự kiến đến**: `$arrTime` (~{$t['duration_minutes']} phút)\n";
            $reply .= "- 💰 **Giá vé niêm yết**: **$price**\n";
            $reply .= "- 💺 **Dòng xe**: " . ucfirst($t['bus_type']) . " - Tổng số: {$t['total_seats']} ghế\n";
            $reply .= "- 🟢 **Ghế trống khả dụng**: **$availableSeats** / {$t['total_seats']} chỗ\n";
            $reply .= "- ⚙️ **Trạng thái**: " . ($t['status'] === 'scheduled' ? 'Đang mở bán vé 🛒' : $t['status']) . "\n\n";
        }
        $reply .= "\n👉 Để đặt vé các chuyến này, vui lòng truy cập trang chủ tìm kiếm chuyến xe hoặc yêu cầu mình tra cứu cụ thể hơn.";
        return $reply;
    }
}
