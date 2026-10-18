# Hệ thống website đặt vé xe khách

## Tech stack

- PHP 8.x, MySQL, PDO
- MVC tự xây
- HTML/CSS/JavaScript, Bootstrap 5 CDN
- Fetch API cho tìm chuyến, chọn ghế, đặt vé, chatbot, gợi ý chuyến

## Cách chạy bằng XAMPP/Laragon

1. Đặt project trong `htdocs` hoặc virtual host.
2. Trỏ document root vào thư mục `public/`.
3. Với XAMPP hiện tại có thể mở: `http://localhost/lobibus-1/public/`.
4. Nếu dùng virtual host, đặt `BASE_URL` trong `config/app.php` theo domain local.

## Database

1. Mở phpMyAdmin hoặc MySQL CLI.
2. Import `database/schema.sql`.
3. Import `database/seed.sql`.

Tài khoản admin mẫu: `admin@lobibus.local` / `admin123`.

## Cấu trúc thư mục

- `app/Core`: App, Router, Controller, Model, Database, Session, Auth, Helper.
- `app/Controllers`: controller khách hàng, API, admin.
- `app/Models`: model skeleton cho user, tuyến, xe, ghế, chuyến, booking, ticket, payment.
- `app/Services`: service skeleton cho auth, booking, ticket, payment, QR, mail, chatbot, recommendation.
- `app/Views`: layout và view PHP.
- `public`: entry point `index.php` và assets public.
- `database`: schema, seed, backup placeholder.
- `legacy`: bản sao HTML cũ để tham khảo.

## Frontend đã migrate

- `index.html` -> `app/Views/home/index.php`
- `DatChuyen.html` -> `app/Views/trips/search.php`
- `LichTrinh.html` và `ChiTietTuyen.html` -> `app/Views/trips/detail.php` / `app/Views/trips/list.php`
- `xe32cho.html`, `xegiuongnam.html`, `xelimousine.html` -> `app/Views/bookings/select-seat.php`
- `TraCuu.html` -> `app/Views/bookings/history.php`
- Các file HTML còn lại đã copy vào `legacy/` để nhóm tách dần thành view phụ.
- CSS/JS/images cũ đã được rà soát. Chỉ giữ lại asset đang được MVC mới dùng trực tiếp trong `public/assets`.
- HTML cũ vẫn nằm trong `legacy/` để tham khảo nội dung và layout.

## Dữ liệu hard-code đã chuyển

- Một phần tuyến mẫu từ giao diện cũ: Hà Nội -> Hải Phòng, Hà Nội -> Nam Định, Hà Nội -> Ninh Bình, Hà Nội -> Thanh Hóa.
- Xe mẫu: ghế ngồi, giường nằm, limousine.
- Ghế mẫu cho xe 32 chỗ.
- Câu hỏi chatbot mẫu.

## Đã chạy được

- `public/index.php` route trang chủ, login, register, search trip, chọn ghế, chatbot, admin dashboard.
- API demo: `/api/trips/search`, `/api/seats`, `/api/bookings/create`, `/api/chatbot/reply`, `/api/recommendations`.
- Chưa cần database để xem trang chủ và gọi API demo vì model hiện trả dữ liệu mẫu.

## TODO cho nhóm

- Thành viên Auth: hoàn thiện `AuthService`, model `User`, middleware phân quyền admin/customer, validate form.
- Thành viên Booking: nối `Trip`, `Seat`, `Booking`, `BookingDetail`, `Ticket`, transaction chống trùng ghế.
- Thành viên Payment: hoàn thiện `PaymentService`, cập nhật trạng thái thanh toán, trang kết quả.
- Thành viên Admin: CRUD locations, routes, buses, seats, trips, bookings, payments, statistics.
- Thành viên Frontend: tách tiếp HTML trong `legacy/` thành partial view, sửa link nội bộ từ `.html` sang route MVC.
- Thành viên API/JS: nếu cần khôi phục logic từ giao diện cũ, lấy từ `legacy/` hoặc lịch sử git rồi viết lại theo API/database mới.
- Thành viên QR/Mail: cài `endroid/qr-code` hoặc `chillerlan/php-qrcode`, cài `phpmailer/phpmailer`, hoàn thiện service.
- Thành viên Map/AI: tích hợp Leaflet/OpenStreetMap, chatbot rule-based từ bảng `chatbot_questions`, gợi ý chuyến từ lịch sử đặt vé.
