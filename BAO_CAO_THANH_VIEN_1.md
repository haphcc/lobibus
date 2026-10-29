# Bao cao cong viec Thanh vien 1

Nguon doi chieu: `PHAN_CONG_5_THANH_VIEN.md`, muc **Thanh vien 1: Core MVC, Auth, Session, phan quyen**.

## Tong quan cac file da thay doi

- `app/Services/AuthService.php`: hoan thien dang ky, dang nhap, validate, hash password va lay role customer.
- `app/Models/User.php`: bo sung truy van user kem role, tao user voi password hash, verify login tra ve thong tin user.
- `app/Models/Role.php`: bo sung tim role theo ten.
- `app/Controllers/AuthController.php`: xu ly form login/register/logout bang `AuthService`, flash message va redirect an toan.
- `app/Core/Auth.php`: quan ly session user, login/logout, check admin/customer va user id.
- `app/Core/Session.php`: bo sung flash message.
- `app/Core/Router.php`: chan route `/admin` neu khong phai admin.
- `app/Views/auth/login.php`, `app/Views/auth/register.php`: form dung `url()`, hien thi loi/thanh cong, giu lai input cu.
- `app/Views/layouts/navbar.php`, `app/Views/layouts/sidebar.php`: hien thi nut theo session va link admin dung helper.

## 1. Hoan thien AuthService

### Da lam

- Tao `AuthService` co dependency den `User` va `Role`: `app/Services/AuthService.php:5-18`.
- Hoan thien dang ky user moi trong `register()`: `app/Services/AuthService.php:21-69`.
- Validate ho ten, email, mat khau, xac nhan mat khau va email trung: `app/Services/AuthService.php:23-47`.
- Lay role `customer` tu database, khong hard-code admin/customer theo session gia: `app/Services/AuthService.php:49-52`.
- Hash password bang `password_hash`: `app/Services/AuthService.php:54-60`.
- Hoan thien dang nhap bang `verifyLogin()`: `app/Services/AuthService.php:71-79`.

### Cach kiem chung

1. Import `database/schema.sql` va `database/seed.sql`.
2. Mo `http://localhost/lobibus-1/public/register`, dang ky tai khoan moi voi email hop le va mat khau tu 6 ky tu.
3. Kiem tra bang `users`: password phai la chuoi hash, khong phai mat khau plaintext.
4. Dang nhap tai `http://localhost/lobibus-1/public/login` bang tai khoan vua tao.
5. Thu dang ky trung email: form phai bao loi `Email nay da duoc dang ky.`.
6. Thu nhap sai mat khau: form login phai bao loi va khong tao session dang nhap.

## 2. Hoan thien User model

### Da lam

- `find()` lay user kem ten role qua join `roles`: `app/Models/User.php:22-34`.
- `findByEmail()` lay user theo email kem role: `app/Models/User.php:36-48`.
- `create()` nhan `password_hash` neu co, neu khong thi tu hash bang `password_hash`: `app/Models/User.php:50-70`.
- Chuan hoa email ve lowercase khi create/update: `app/Models/User.php:64`, `app/Models/User.php:78`.
- `verifyLogin()` tra ve thong tin user neu account active va `password_verify()` dung, nguoc lai tra `null`: `app/Models/User.php:107-119`.

### Cach kiem chung

1. Dang ky tai khoan moi, sau do kiem tra record trong bang `users`.
2. Email luu trong database phai o dang lowercase.
3. Password trong database phai bat dau bang dinh dang hash, vi du `$2y$...`.
4. Dang nhap dung email/mat khau: navbar phai hien ten user va nut `Dang xuat`.
5. Dang nhap tai khoan bi khoa trong seed, vi du `locked.customer@lobibus.local`: phai bi tu choi vi status khong phai `active`.

## 3. Hoan thien Role model

### Da lam

- Giu ham lay danh sach role `all()`: `app/Models/Role.php:8-12`.
- Them `findByName()` de tim role theo ten, dang dung khi dang ky customer: `app/Models/Role.php:14-20`.
- `AuthService` goi `findByName('customer')` truoc khi tao user: `app/Services/AuthService.php:49-55`.

### Cach kiem chung

1. Kiem tra seed co role `customer` trong `database/seed.sql`.
2. Dang ky tai khoan moi.
3. Kiem tra cot `role_id` cua user moi phai tro den role `customer`.
4. Neu tam thoi xoa/doi ten role `customer` trong database test, dang ky phai that bai voi loi khong tim thay role customer.

## 4. Lam middleware/check phan quyen

### Da lam

- Them `Auth::login()` luu session user va regenerate session id: `app/Core/Auth.php:9-15`.
- Them `Auth::logout()`: `app/Core/Auth.php:17-20`.
- Them `Auth::isAdmin()`, `Auth::isCustomer()`, `Auth::id()`: `app/Core/Auth.php:32-46`.
- Chuan hoa du lieu user dua vao session: `app/Core/Auth.php:48-59`.
- Them flash message cho session: `app/Core/Session.php:31-40`.
- Chan moi route `/admin` va `/admin/...` trong router neu user khong phai admin: `app/Core/Router.php:22-29`, `app/Core/Router.php:70-73`.
- Redirect ve login kem tham so `redirect` khi bi chan admin: `app/Core/Router.php:25-28`.
- Sau login, redirect ve trang du dinh hoac `/admin` neu la admin: `app/Controllers/AuthController.php:40-45`, `app/Controllers/AuthController.php:100-112`.

### Cach kiem chung

1. Chua dang nhap, mo `http://localhost/lobibus-1/public/admin`.
2. He thong phai chuyen ve `/login?redirect=%2Fadmin` va hien thong bao can dang nhap admin.
3. Dang nhap bang tai khoan customer moi tao, sau do mo lai `/admin`: van bi chan.
4. Dang nhap bang admin seed `admin@lobibus.local` / `admin123`: phai vao duoc dashboard admin.
5. Bam `Dang xuat`, sau do mo lai `/admin`: phai bi chuyen ve login.

## 5. Sua layout navbar va admin sidebar

### Da lam

- Navbar doc user tu session bang `Auth::user()` va check admin bang `Auth::isAdmin()`: `app/Views/layouts/navbar.php:1-3`.
- Khi da dang nhap, navbar hien ten user, nut admin neu co quyen admin va nut dang xuat: `app/Views/layouts/navbar.php:22-28`.
- Khi chua dang nhap, navbar hien nut dang nhap/dang ky: `app/Views/layouts/navbar.php:29-32`.
- Tat ca link navbar dung `url()` de chay duoc trong subfolder XAMPP: `app/Views/layouts/navbar.php:7`, `app/Views/layouts/navbar.php:15-19`, `app/Views/layouts/navbar.php:26-31`.
- Sidebar admin co day du link den cac module admin va nut dang xuat: `app/Views/layouts/sidebar.php:7-17`.
- Form login dung `url('/login')`, hien thi loi/thanh cong, giu email cu va giu hidden redirect: `app/Views/auth/login.php:8-22`.
- Form register dung `url('/register')`, hien thi loi, giu input cu, them phone va password confirmation: `app/Views/auth/register.php:5-25`.

### Cach kiem chung

1. Mo trang chu khi chua dang nhap: navbar phai hien `Dang nhap` va `Dang ky`.
2. Dang nhap bang customer: navbar phai hien ten user va `Dang xuat`, khong hien nut `Admin`.
3. Dang nhap bang admin: navbar phai hien ten user, nut `Admin` va `Dang xuat`.
4. Vao `/admin`: sidebar phai co link Dashboard, Users, Locations, Routes, Buses, Seats, Trips, Bookings, Payments va Dang xuat.
5. Chay project trong subfolder `http://localhost/lobibus-1/public/`, bam cac link navbar/sidebar: link phai di theo base URL, khong bi quay ve root `/`.

## 6. Kiem tra route khi chay bang XAMPP

### Da doi chieu

- Route auth dang co trong `public/index.php:49-53`: `/login`, `/register`, `/logout`.
- Route admin dashboard va module admin dang co trong `public/index.php:66-105`.
- Guard admin nam o router nen ap dung cho tat ca route admin, khong chi rieng `/admin`: `app/Core/Router.php:22-29`.

### Cach kiem chung

1. Mo `http://localhost/lobibus-1/public/`: trang chu phai hien binh thuong.
2. Mo `http://localhost/lobibus-1/public/login`: form dang nhap phai hien.
3. Mo `http://localhost/lobibus-1/public/register`: form dang ky phai hien.
4. Mo `http://localhost/lobibus-1/public/admin` khi chua dang nhap: phai bi redirect ve login.
5. Dang nhap admin, sau do mo:
   - `http://localhost/lobibus-1/public/admin`
   - `http://localhost/lobibus-1/public/admin/users`
   - `http://localhost/lobibus-1/public/admin/trips`
6. Cac URL tren khong duoc 404 va phai nam trong layout admin.

## Ket qua ban giao theo phan cong

- Dang ky user moi da co validate, hash password va gan role customer tu database.
- Dang nhap dung `password_verify`, chi chap nhan user active.
- Dang xuat xoa session.
- Admin route bi chan neu chua dang nhap admin.
- Navbar/sidebar hien thi theo session va dung helper `url()` de chay trong subfolder XAMPP.

## Viec can bo sung tiep cho Thanh vien 1

### 1. Nang cap policy mat khau khi dang ky

- Cap nhat `app/Services/AuthService.php` de validate mat khau manh hon.
- De xuat policy:
  - Toi thieu 8 ky tu.
  - Co it nhat 1 chu in hoa.
  - Co it nhat 1 chu thuong.
  - Co it nhat 1 chu so.
  - Co it nhat 1 ky tu dac biet, vi du `!@#$%^&*`.
- Cap nhat thong bao loi trong form `app/Views/auth/register.php` de nguoi dung biet yeu cau mat khau.
- Cap nhat test dang ky:
  - Mat khau ngan hon 8 ky tu phai bi tu choi.
  - Mat khau khong co chu in hoa phai bi tu choi.
  - Mat khau khong co ky tu dac biet phai bi tu choi.
  - Mat khau hop le vi du `Lobibus@123` phai dang ky duoc.

### 2. Them chuc nang quen mat khau

- Them route trong `public/index.php`:
  - `GET /forgot-password`
  - `POST /forgot-password`
- Them action moi trong `app/Controllers/AuthController.php`, vi du `forgotPassword()`.
- Tao view moi `app/Views/auth/forgot-password.php` gom:
  - O nhap email.
  - Nut gui mat khau tam thoi ve email.
  - Link quay lai dang nhap.
- Them link/nut `Quen mat khau?` trong `app/Views/auth/login.php`.
- Bo sung ham trong `app/Models/User.php` de cap nhat password hash theo user id/email, vi du `updatePassword()`.
- Bo sung logic trong `app/Services/AuthService.php`:
  - Kiem tra email hop le.
  - Tim user active theo email.
  - Sinh mat khau tam thoi manh.
  - Hash mat khau tam thoi bang `password_hash`.
  - Cap nhat password vao database.
  - Goi `MailService` de gui mat khau tam thoi cho nguoi dung.
- Luu y bao mat: neu kip, nen lam reset link/token thay vi gui mat khau qua email. Neu van lam theo yeu cau hien tai, email chi nen gui mat khau tam thoi va yeu cau nguoi dung doi lai sau khi dang nhap.

### 3. Cai va cau hinh PHPMailer

- Cap nhat `composer.json`:
  - Them dependency `phpmailer/phpmailer`.
- Chay:

```bash
composer require phpmailer/phpmailer
```

- Cap nhat `public/index.php` de load `vendor/autoload.php` neu project chua load Composer autoload.
- Hoan thien `app/Services/MailService.php` bang PHPMailer:
  - Doc SMTP tu `config/mail.php`.
  - Dat `CharSet = 'UTF-8'`.
  - Gui email text/plain hoac HTML don gian.
  - Xu ly loi gui mail ro rang.
- Cap nhat `config/mail.php` theo SMTP that khi demo, khong commit mat khau SMTP that neu repo public.

### 4. Gia co bao mat session cookie

- Cap nhat `app/Core/Session.php` trong `start()`:
  - `httponly => true`
  - `samesite => 'Lax'`
  - `secure => true` khi chay HTTPS
  - `lifetime => 0`
- Cap nhat `destroy()`:
  - Kiem tra session dang active truoc khi `session_destroy()`.
  - Xoa cookie session khi logout.
- Giu `session_regenerate_id(true)` sau khi login trong `Auth::login()` de han che session fixation.

### 5. Chinh lai header/footer cho dep va dung base URL

- Chinh `app/Views/layouts/navbar.php`:
  - Can lai logo, menu, nut dang nhap/dang ky/dang xuat.
  - Dam bao responsive tren mobile.
  - Ten user khong lam vo layout khi qua dai.
- Chinh `app/Views/layouts/footer.php`:
  - Sap xep thanh cac cot thong tin ro rang.
  - Them link nhanh: Dat ve, Lich su dat ve, Ho tro, Goi y chuyen.
  - Them thong tin lien he/hotline/email gon gang.
- Kiem tra `app/Views/layouts/header.php`:
  - Doi link `href="/"` thanh `href="<?= url('/') ?>"` neu file nay con duoc dung.
- Chinh CSS trong `public/assets/css/index.css` hoac `public/assets/css/customer.css`:
  - Header sticky gon, mau sac dong nhat voi LobiBus.
  - Footer nen toi, chu de doc, spacing deu.
  - Kiem tra tren desktop va mobile.

### 6. Kiem tra lai sau khi sua

- Dang ky voi mat khau sai policy: phai hien loi dung.
- Dang ky voi mat khau hop le: tao user va hash password dung.
- Bam `Quen mat khau?`, nhap email user active: he thong cap nhat mat khau tam thoi va gui email.
- Dang nhap bang mat khau tam thoi vua nhan.
- Dang xuat phai xoa session/cookie hop le.
- Mo lai cac route:
  - `http://localhost/lobibus-1/public/login`
  - `http://localhost/lobibus-1/public/register`
  - `http://localhost/lobibus-1/public/forgot-password`
  - `http://localhost/lobibus-1/public/admin`
- Kiem tra header/footer khong vo tren mobile va khong sai link khi chay trong subfolder XAMPP.

