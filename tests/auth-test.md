# Auth test cases - Thành viên 1

## Đăng ký với policy mật khẩu mới

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập họ tên, email mới và mật khẩu `Lobi1!`.
3. Kết quả mong đợi: form báo lỗi `Mật khẩu phải có ít nhất 8 ký tự.`.

## Đăng ký thiếu chữ in hoa

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập email mới và mật khẩu `lobibus@123`.
3. Kết quả mong đợi: form báo lỗi `Mật khẩu phải có ít nhất 1 chữ in hoa.`.

## Đăng ký thiếu ký tự đặc biệt

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập email mới và mật khẩu `Lobibus123`.
3. Kết quả mong đợi: form báo lỗi `Mật khẩu phải có ít nhất 1 ký tự đặc biệt`.

## Đăng ký thành công

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập email mới và mật khẩu `Lobibus@123`.
3. Kết quả mong đợi: hệ thống chuyển về trang đăng nhập và hiện thông báo đăng ký thành công.
4. Kiểm tra bảng `users`: cột `password` là hash, không phải `Lobibus@123`.

## Quên mật khẩu

1. Mở `http://localhost/lobibus-1/public/forgot-password`.
2. Nhập email của user active.
3. Kết quả mong đợi: hệ thống thông báo đã gửi mật khẩu tạm thời, email vừa nhập vẫn còn trong ô email.
4. Bên dưới nút gửi phải hiện ô `Mật khẩu tạm thời từ email`.
5. Nếu `config/mail.php` đang để `mailer => log`, mở `public/uploads/mail.log` để lấy mật khẩu tạm thời.
6. Nhập mật khẩu tạm thời vào ô mới và bấm `Xác nhận và đăng nhập`.
7. Kết quả mong đợi: đăng nhập thành công bằng mật khẩu tạm thời.

## Chặn admin route

1. Đăng xuất khỏi hệ thống.
2. Mở `http://localhost/lobibus-1/public/admin`.
3. Kết quả mong đợi: bị chuyển về `/login?redirect=%2Fadmin`.
4. Đăng nhập bằng `admin@lobibus.local` / `admin123`.
5. Kết quả mong đợi: vào được dashboard admin.
