# Auth test cases - Thành viên 1

## Đăng ký với policy mật khẩu mới

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập họ tên, email mới, số điện thoại hợp lệ và mật khẩu `1234567`.
3. Kết quả mong đợi: form báo lỗi `Mật khẩu phải có ít nhất 8 ký tự.`.

## Đăng ký với mật khẩu 8 ký tự

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập email mới, số điện thoại hợp lệ và mật khẩu `12345678` hoặc `abcdefgh`.
3. Kết quả mong đợi: không còn lỗi thiếu chữ hoa, chữ thường, chữ số hoặc ký tự đặc biệt.

## Đăng ký thiếu số điện thoại

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập họ tên, email mới, bỏ trống ô số điện thoại và nhập mật khẩu `Lobibus@123`.
3. Kết quả mong đợi: trình duyệt yêu cầu nhập số điện thoại; nếu gửi được lên server thì form báo lỗi `Vui lòng nhập số điện thoại.`.

## Đăng ký sai định dạng số điện thoại

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập số điện thoại `12345` hoặc `12345678901`, các thông tin còn lại hợp lệ.
3. Kết quả mong đợi: form không cho gửi hoặc server báo số điện thoại phải gồm đúng 10 chữ số.

## Đăng ký với số điện thoại hợp lệ

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập số điện thoại `0912345678` hoặc `+84912345678`, các thông tin còn lại hợp lệ.
3. Kết quả mong đợi: hệ thống chấp nhận số điện thoại; trong bảng `users`, số `+84912345678` được lưu chuẩn hóa thành `0912345678`.

## Đăng ký thành công

1. Mở `http://localhost/lobibus-1/public/register`.
2. Nhập email mới, số điện thoại hợp lệ và mật khẩu `12345678`.
3. Kết quả mong đợi: hệ thống chuyển về trang đăng nhập và hiện thông báo đăng ký thành công.
4. Kiểm tra bảng `users`: cột `password` là hash, không phải `12345678`.

## Cập nhật thông tin tài khoản

1. Đăng nhập bằng tài khoản customer.
2. Mở `http://localhost/lobibus-1/public/account`.
3. Sửa họ tên và số điện thoại thành `0912345678`, bấm `Lưu thông tin`.
4. Kết quả mong đợi: hiện thông báo cập nhật thành công, navbar/session hiển thị tên mới.
5. Nhập số điện thoại `12345` hoặc `12345678901`.
6. Kết quả mong đợi: form báo lỗi số điện thoại phải gồm đúng 10 chữ số.

## Đổi mật khẩu không dùng OTP

1. Đăng nhập và mở `http://localhost/lobibus-1/public/account`.
2. Nhập mật khẩu hiện tại sai, nhập mật khẩu mới và xác nhận mật khẩu.
3. Kết quả mong đợi: form báo lỗi `Mật khẩu hiện tại không chính xác.`.
4. Nhập mật khẩu hiện tại đúng, nhập mật khẩu mới ít hơn 8 ký tự.
5. Kết quả mong đợi: form báo lỗi `Mật khẩu phải có ít nhất 8 ký tự.`.
6. Nhập mật khẩu hiện tại đúng, nhập mật khẩu mới hợp lệ nhưng xác nhận mật khẩu không khớp.
7. Kết quả mong đợi: form báo lỗi `Xác nhận mật khẩu không khớp.`.
8. Nhập mật khẩu hiện tại đúng, nhập mật khẩu mới hợp lệ và xác nhận mật khẩu khớp.
9. Kết quả mong đợi: đổi mật khẩu thành công; đăng xuất và đăng nhập lại bằng mật khẩu mới được.

## Đăng nhập bằng Google

1. Cấu hình `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` trong `.env`.
2. Trong Google Cloud Console, thêm redirect URI đúng bằng `GOOGLE_REDIRECT_URI`.
3. Mở `http://localhost/lobibus-1/public/login`, bấm `Đăng nhập bằng Google`.
4. Nếu email Google trùng user active: đăng nhập vào user đó.
5. Nếu email Google trùng user locked: hệ thống báo tài khoản bị khóa.
6. Nếu email Google chưa tồn tại: hệ thống tạo customer mới và đăng nhập.
7. Xóa cấu hình Google và thử lại.
8. Kết quả mong đợi: login page hiển thị lỗi cấu hình thân thiện, không crash.

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
