# Lobibus — Hệ thống website đặt vé xe khách

Phiên bản: cập nhật mã nguồn mới nhất.

## Tóm tắt

Ứng dụng là một hệ thống đặt vé xe khách xây dựng theo mô hình MVC bằng PHP, sử dụng MySQL và các service hỗ trợ.

## Yêu cầu môi trường

- XAMPP (PHP 8.x, MySQL)

## Hướng dẫn cài đặt

1. Giải nén/Copy thư mục project vào thư mục `htdocs` của XAMPP.
2. Đảm bảo tên thư mục là `lobibus` (để đường dẫn là `htdocs/lobibus`).
3. (Lưu ý: Thư mục `vendor` đã được bao gồm sẵn, không cần chạy composer).

## Database

Dữ liệu nằm trong thư mục `database/`:
- `database/schema.sql`: Cấu trúc bảng.
- `database/seed.sql`: Dữ liệu mẫu (Tài khoản admin: `admin@lobibus.local` / `admin123`).

**Cô vui lòng import hai file này vào MySQL (trên phpMyAdmin) để ứng dụng hoạt động.**

## Cách chạy ứng dụng

1. Mở XAMPP Control Panel, Start **Apache** và **MySQL**.
2. Truy cập vào trình duyệt theo đường dẫn:
   `http://localhost/lobibus/public`

---

## Cấu trúc chính

- `app/Controllers`: Xử lý logic.
- `app/Models`: Tương tác cơ sở dữ liệu.
- `app/Services`: Các dịch vụ (Thanh toán, Gửi mail, QR Code, Chatbot).
- `app/Views`: Giao diện người dùng.
- `public/`: Điểm truy cập chính của ứng dụng và các tài sản tĩnh (CSS, JS, Images).
- `config/`: Cấu hình hệ thống.
- `database/`: File SQL để khởi tạo dữ liệu.

## Một số API chính

- `/api/trips/search` — Tìm kiếm chuyến xe.
- `/api/seats` — Lấy thông tin sơ đồ ghế.
- `/api/bookings/create` — Thực hiện đặt vé.
- `/api/chatbot/reply` — Phản hồi tin nhắn chatbot.
