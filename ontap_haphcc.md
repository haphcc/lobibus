# LobiBus - Tài liệu ôn tập thuyết trình

Tài liệu này tóm tắt cách chạy project và vai trò các file, hàm quan trọng để bạn ôn trước khi demo.

## 1. Cách chạy project

### Yêu cầu môi trường

- PHP 8.x
- MySQL / MariaDB
- XAMPP hoặc Laragon
- Trình duyệt web

### Các bước chạy

1. Đặt project vào `htdocs` hoặc tạo virtual host trỏ tới thư mục gốc của project.
2. Trỏ document root vào thư mục `public/`.
3. Import database theo thứ tự:
   - `database/schema.sql`
   - `database/seed.sql`
4. Nếu chạy trên XAMPP Windows, nên dùng `utf8mb4` để tránh lỗi tiếng Việt.
5. Mở project bằng đường dẫn local, ví dụ `http://localhost/lobibus-1/public/`.
6. Nếu dùng virtual host, kiểm tra lại `BASE_URL` trong `config/app.php`.

### Tài khoản mẫu

- Admin mẫu: `admin@lobibus.local`
- Mật khẩu: `admin123`

### Biến cấu hình quan trọng

- `OPENAI_API_KEY`: dùng cho chatbot AI.
- `PAYOS_CLIENT_ID`, `PAYOS_API_KEY`, `PAYOS_CHECKSUM_KEY`: dùng cho PayOS.

## 2. Luồng tổng quát để nhớ khi thuyết trình

Luồng chính của dự án là:

`route -> controller -> service/model -> view/js`

Ý nghĩa:

- `route`: khai báo URL trong `public/index.php`
- `controller`: nhận request và điều hướng
- `service/model`: xử lý nghiệp vụ hoặc truy vấn dữ liệu
- `view/js`: hiển thị giao diện và chạy tương tác frontend

## 3. Mảng 1: Gợi ý chuyến

### File chính

- `public/index.php`: khai báo route `/recommendations` và `/api/recommendations`
- `app/Controllers/RecommendationController.php`: mở trang gợi ý chuyến
- `app/Controllers/Api/RecommendationApiController.php`: trả dữ liệu gợi ý dạng JSON
- `app/Services/RecommendationService.php`: lớp trung gian gọi model
- `app/Models/Recommendation.php`: xử lý logic gợi ý thật sự
- `app/Views/trips/list.php`: giao diện danh sách gợi ý
- `public/assets/js/recommendation.js`: render dữ liệu động, lọc và sắp xếp trên frontend

### Hàm cần nhớ

#### `RecommendationController::index()`

- Mở view `trips.list`.
- Vai trò: chỉ là trang hiển thị.

#### `RecommendationApiController::suggest()`

- Gọi `RecommendationService` và trả JSON.
- Vai trò: API cho frontend lấy danh sách chuyến đề xuất.

#### `RecommendationService::suggest(array $context = [])`

- Nhận dữ liệu đầu vào rồi gọi model `Recommendation`.
- Vai trò: lớp trung gian, tách controller khỏi SQL.

#### `Recommendation::suggestTrips(array $context = [])`

- Tạo 4 nhóm gợi ý:
  - chuyến rẻ nhất
  - khởi hành sớm nhất
  - còn nhiều ghế nhất
  - phổ biến nhất
- Vai trò: gom dữ liệu và gắn nhãn lý do gợi ý.

#### `Recommendation::queryTrips(string $type)`

- Query chuyến theo từng tiêu chí.
- Vai trò: quyết định cách sắp xếp dữ liệu.

### Ý để nói khi demo

- User vào trang gợi ý chuyến.
- Frontend gọi API `/api/recommendations`.
- Backend trả danh sách chuyến theo từng tiêu chí.
- JavaScript render card chuyến và cho phép lọc.

## 4. Mảng 2: Chatbot AI

### File chính

- `public/index.php`: route `/chatbot` và `/api/chatbot/reply`
- `app/Controllers/ChatbotController.php`: mở chatbot tự động bằng `open_chat=1`
- `app/Controllers/Api/ChatbotApiController.php`: nhận câu hỏi và trả câu trả lời
- `app/Services/ChatbotService.php`: logic AI chính
- `app/Models/Chatbot.php`: tìm câu trả lời FAQ trong bảng `chatbot_questions`
- `app/Views/chatbot/bubble.php`: giao diện chat nổi
- `public/assets/js/chatbot.js`: gửi tin nhắn, nhận response, mở/đóng khung chat

### Hàm cần nhớ

#### `ChatbotController::index()`

- Redirect về trang chủ với tham số `open_chat=1`.
- Vai trò: mở chatbot tự động.

#### `ChatbotApiController::reply()`

- Đọc message từ body request.
- Gọi `ChatbotService::reply()`.
- Trả JSON `{ reply: ... }`.

#### `ChatbotService::clearHistory()`

- Xóa lịch sử chat trong session.
- Vai trò: reset hội thoại.

#### `ChatbotService::reply(string $message)`

- Hàm chính của chatbot.
- Nếu có `OPENAI_API_KEY` thì gọi AI.
- Nếu không có key thì dùng fallback local.
- Vai trò: điều phối toàn bộ luồng trả lời.

#### `ChatbotService::callOpenAI(array $messages)`

- Gọi OpenAI Chat Completions.
- Vai trò: nhận phản hồi AI dạng JSON.

#### `ChatbotService::executeTool(string $tool, array $arguments)`

- Chọn tool phù hợp theo câu hỏi.
- Vai trò: chạy truy vấn dữ liệu an toàn.

#### `ChatbotService::getSystemPrompt()`

- Trả về prompt hướng dẫn AI.
- Vai trò: ép AI trả lời đúng định dạng và không bịa dữ liệu.

#### `ChatbotService::localSmartReply(string $message)`

- Luồng fallback khi không có API key hoặc AI lỗi.
- Vai trò: xử lý tìm chuyến, tra booking, hỏi FAQ, tra tuyến.

#### `ChatbotService::dbSearchTrips(string $from, string $to, ?string $date = null)`

- Tìm chuyến theo nơi đi, nơi đến, ngày.
- Vai trò: trả danh sách chuyến xe thực tế.

#### `ChatbotService::dbGetBookingStatus(string $code)`

- Tra booking theo mã, số điện thoại hoặc email.
- Vai trò: xem tình trạng đặt vé và vé.

#### `ChatbotService::dbListRoutes()`

- Lấy danh sách tuyến đang hoạt động.
- Vai trò: trả lời câu hỏi về tuyến đường.

#### `ChatbotService::dbGetTripSeats(int $tripId)`

- Lấy số ghế trống và ghế đã đặt.
- Vai trò: hỏi sơ đồ ghế của chuyến.

#### `ChatbotService::dbGetReviews()`

- Lấy đánh giá gần đây.
- Vai trò: trả lời câu hỏi về review.

#### `ChatbotService::formatBookingStatusResponse(array $bookings)`

- Định dạng kết quả tra cứu booking thành đoạn trả lời dễ đọc.

#### `ChatbotService::formatTripsResponse(string $from, string $to, array $trips)`

- Định dạng danh sách chuyến theo văn bản đẹp.

#### `Chatbot::findAnswer(string $question)`

- Tìm FAQ trong bảng `chatbot_questions` bằng keyword hoặc câu hỏi gần đúng.

#### `Chatbot::normalizeText(string $text)`

- Chuẩn hóa tiếng Việt không dấu để so khớp dễ hơn.

### Ý để nói khi demo

- Chatbot có 2 chế độ:
  - AI online với OpenAI
  - fallback local nếu không có key
- AI có thể gọi tool để tra cứu dữ liệu thật.
- Đây là điểm mạnh vì hạn chế trả lời bịa.

## 5. Mảng 3: Dashboard admin

### File chính

- `public/index.php`: route `/admin`
- `app/Controllers/Admin/AdminController.php`: controller cha cho admin
- `app/Controllers/Admin/DashboardController.php`: trang dashboard chính
- `app/Models/Statistic.php`: lấy toàn bộ số liệu thống kê
- `app/Views/admin/dashboard/index.php`: view dashboard chính
- `app/Views/admin/statistics/index.php`: view thống kê chi tiết dùng chung
- `app/Views/layouts/admin.php`: layout riêng cho admin
- `app/Views/layouts/sidebar.php`: sidebar admin

### Hàm cần nhớ

#### `AdminController::redirect(string $path, string $type = '', string $message = '')`

- Redirect kèm flash message.
- Vai trò: helper chung cho toàn bộ admin.

#### `DashboardController::index()`

- Khởi tạo `Statistic`.
- Trả dữ liệu cho view dashboard.
- Vai trò: màn hình tổng quan admin.


#### `Statistic::dashboardSummary()`

- Trả số lượng users, trips, bookings, tickets, revenue.

#### `Statistic::revenueByDay(int $days = 7)`

- Doanh thu theo ngày khởi hành.

#### `Statistic::bookingStatusBreakdown()`

- Thống kê trạng thái booking.

#### `Statistic::paymentMethodBreakdown()`

- Thống kê theo phương thức thanh toán.

#### `Statistic::tripStatusBreakdown()`

- Thống kê trạng thái chuyến xe.

#### `Statistic::usersByRole()`

- Đếm số user theo vai trò.

#### `Statistic::topRoutes(int $limit = 5)`

- Tìm tuyến có nhiều booking nhất.

#### `Statistic::upcomingTrips(int $limit = 6)`

- Liệt kê các chuyến sắp chạy.

#### `Statistic::recentBookings(int $limit = 8)`

- Lấy các booking mới nhất.

### Ý để nói khi demo

- Dashboard chính là màn hình tổng quan.
- View thống kê dùng chung cùng model `Statistic`.
- Nếu giáo viên hỏi vì sao có `DashboardController` và `StatisticController`, bạn có thể nói: tách để phân biệt màn hình tổng quan và màn hình thống kê chi tiết, dù hiện tại dashboard đang reuse cùng view dữ liệu.

## 6. Mảng 4: PayOS

### File chính

- `public/index.php`: route `/payment/method`, `/payment/confirm`, `/payment/result`
- `app/Controllers/PaymentController.php`: điều phối toàn bộ luồng thanh toán
- `app/Services/PayOSService.php`: gọi API PayOS thật
- `app/Models/Booking.php`: lấy và cập nhật booking
- `app/Models/Payment.php`: lấy và cập nhật payment
- `app/Models/Ticket.php`: cập nhật vé và QR
- `app/Services/QRCodeService.php`: tạo file QR code
- `app/Services/TicketEmailService.php`: gửi email vé
- `app/Views/payments/payment-method.php`: trang hiển thị QR và nút kiểm tra thanh toán

### Hàm cần nhớ

#### `PaymentController::method()`

- Kiểm tra quyền truy cập booking.
- Lấy payment tương ứng.
- Gọi PayOS để tạo payment link.
- Hiển thị QR và thông tin thanh toán.

#### `PaymentController::confirm()`

- Người dùng bấm kiểm tra thanh toán.
- Controller hỏi PayOS xem trạng thái đã `PAID` chưa.
- Nếu đã thanh toán thì cập nhật payment, booking, ticket.

#### `PaymentController::result()`

- Kiểm tra lại trạng thái PayOS sau khi quay về.
- Nếu đã trả tiền thì mark paid và xác nhận vé.

#### `PaymentController::methodUrl(int $bookingId, array $source)`

- Tạo URL quay lại màn hình thanh toán.

#### `PaymentController::successRedirect(array $booking, array $source)`

- Quyết định sau khi thanh toán thành công thì chuyển tới đâu.

#### `PaymentController::paymentQrDataUri(string $qrCode)`

- Chuyển mã QR sang SVG base64 để hiển thị trực tiếp trên web.

#### `PaymentController::refreshConfirmedTicketAndEmail(int $bookingId)`

- Tạo lại QR ticket và gửi email vé sau khi thanh toán xong.

#### `PaymentController::payosDataWithCachedQr(int $bookingId, array $payosData)`

- Giữ lại dữ liệu PayOS trong session nếu API trả thiếu.

#### `PaymentController::absoluteUrl(string $path)`

- Tạo URL đầy đủ cho callback của PayOS.

#### `PayOSService::__construct()`

- Đọc config PayOS từ file cấu hình.

#### `PayOSService::isConfigured()`

- Kiểm tra đã có đủ key PayOS hay chưa.

#### `PayOSService::orderCodeForBooking(int $bookingId)`

- Sinh mã đơn payOS từ booking id.

#### `PayOSService::createPaymentLink(...)`

- Tạo yêu cầu thanh toán trên PayOS.
- Nếu có đơn cũ thì thử lấy lại thay vì lỗi ngay.

#### `PayOSService::getPaymentRequest(int|string $id)`

- Lấy trạng thái thanh toán từ PayOS.

#### `PayOSService::signCreatePayload(array $payload)`

- Ký dữ liệu trước khi gửi lên PayOS.

#### `PayOSService::request(string $method, string $path, ?array $payload = null)`

- Gửi HTTP request tới API PayOS.

#### `PayOSService::assertConfigured()`

- Báo lỗi nếu thiếu key cấu hình PayOS.

#### `PayOSService::description(string $bookingCode, int $orderCode)`

- Tạo mô tả ngắn cho đơn thanh toán.

#### `Booking::getBookingDetailFull(int $id)`

- Lấy đầy đủ thông tin booking, ghế, vé, payment.

#### `Booking::updateStatus(int $id, string $status)`

- Cập nhật trạng thái booking.

#### `Payment::getPaymentByBookingId(int $bookingId)`

- Lấy payment theo booking.

#### `Payment::markPaidByBooking(int $bookingId, string $transactionCode)`

- Đánh dấu payment là đã thanh toán.

#### `Ticket::getTicketByBooking(int $bookingId)`

- Lấy vé theo booking.

#### `Ticket::updateQrPath(int $id, string $path)`

- Cập nhật đường dẫn QR mới.

#### `QRCodeService::generate(string $content, string $ticketCode = '')`

- Sinh file QR code SVG.

#### `TicketEmailService::sendForBooking(int $bookingId)`

- Gửi email vé cho khách sau khi thanh toán thành công.

## 7. Mấy điểm dễ bị hỏi khi thuyết trình

### Vì sao có controller và service riêng?

- Controller nhận request.
- Service xử lý logic phức tạp.
- Model truy vấn dữ liệu.
- Cách này dễ bảo trì hơn.

### Vì sao chatbot có cả AI và fallback local?

- Để không phụ thuộc hoàn toàn vào OpenAI.
- Nếu thiếu API key hoặc lỗi mạng, hệ thống vẫn trả lời được bằng dữ liệu local.

### Vì sao PayOS cần kiểm tra lại sau thanh toán?

- Tránh trường hợp người dùng quét QR xong nhưng hệ thống chưa cập nhật.
- Controller sẽ hỏi lại PayOS để xác nhận trạng thái thực tế.


## 8. Tóm tắt ngắn để học thuộc nhanh

- Gợi ý chuyến: `RecommendationController` -> `RecommendationService` -> `Recommendation`.
- Chatbot AI: `ChatbotController` -> `ChatbotApiController` -> `ChatbotService` -> `Chatbot`.
- Dashboard admin: `DashboardController` -> `Statistic`.
- PayOS: `PaymentController` -> `PayOSService` -> `Booking/Payment/Ticket`.

## 9. Câu chốt khi demo

“LobiBus là website đặt vé xe khách viết theo mô hình MVC tự xây. Em đã tách rõ các phần gợi ý chuyến, chatbot AI, dashboard admin và thanh toán PayOS để dễ bảo trì, dễ mở rộng và dễ kiểm tra luồng xử lý.”