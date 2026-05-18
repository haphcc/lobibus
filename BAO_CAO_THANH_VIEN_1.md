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

## Viec can bo sung tiep cho Thanh vien 1 - da hoan thien

### 1. Nang cap policy mat khau khi dang ky

#### Da lam

- `AuthService` khong con validate toi thieu 6 ky tu truc tiep, ma goi `validatePasswordPolicy()`: `app/Services/AuthService.php:39`.
- Them policy mat khau manh:
  - Toi thieu 8 ky tu: `app/Services/AuthService.php:120-122`.
  - Co it nhat 1 chu in hoa: `app/Services/AuthService.php:124-126`.
  - Co it nhat 1 chu thuong: `app/Services/AuthService.php:128-130`.
  - Co it nhat 1 chu so: `app/Services/AuthService.php:132-134`.
  - Co it nhat 1 ky tu dac biet `!@#$%^&*`: `app/Services/AuthService.php:136-138`.
- Cap nhat form dang ky de hien yeu cau mat khau va doi `minlength` len 8: `app/Views/auth/register.php:19-26`.
- Them test case dang ky/quen mat khau vao `tests/auth-test.md:1-42`.

#### Cach kiem chung

1. Mo `http://localhost/lobibus-1/public/register`.
2. Thu mat khau `Lobi1!`: phai bao loi toi thieu 8 ky tu.
3. Thu mat khau `lobibus@123`: phai bao loi thieu chu in hoa.
4. Thu mat khau `Lobibus123`: phai bao loi thieu ky tu dac biet.
5. Thu mat khau `Lobibus@123`: phai dang ky duoc va password trong database la hash.

### 2. Them chuc nang quen mat khau

#### Da lam

- Them route:
  - `GET /forgot-password`: `public/index.php:58`.
  - `POST /forgot-password`: `public/index.php:59`.
- Them action `forgotPassword()` xu ly 2 buoc tren cung mot trang: gui mat khau tam thoi, sau do xac nhan mat khau tam thoi va dang nhap neu dung: `app/Controllers/AuthController.php:88-150`.
- Tao view moi `app/Views/auth/forgot-password.php:1-54`, gom o email, nut gui mat khau tam thoi, giu lai email trong o input, hien input `Mat khau tam thoi tu email` sau khi gui thanh cong va link quay lai dang nhap/trang chu.
- Them link `Quen mat khau?` trong form dang nhap: `app/Views/auth/login.php:28-31`.
- Them `User::updatePasswordHash()` de cap nhat hash password theo user id: `app/Models/User.php:121-128`.
- Them `AuthService::forgotPassword()`:
  - Validate email: `app/Services/AuthService.php:83-86`.
  - Chi xu ly user active: `app/Services/AuthService.php:88-90`.
  - Sinh mat khau tam thoi manh: `app/Services/AuthService.php:141-165`.
  - Hash mat khau tam thoi bang `password_hash`: `app/Services/AuthService.php:93-95`.
  - Cap nhat database va rollback lai hash cu neu gui email that bai: `app/Services/AuthService.php:97-112`.
  - Tao noi dung email yeu cau user doi lai mat khau sau khi dang nhap: `app/Services/AuthService.php:168-178`.

#### Cach kiem chung

1. Mo `http://localhost/lobibus-1/public/forgot-password`.
2. Nhap email cua user active.
3. Sau khi bam gui, form phai giu lai email da nhap va hien them o `Mat khau tam thoi tu email`.
4. Neu `config/mail.php` dang de `mailer => log`, mo `public/uploads/mail.log` de lay mat khau tam thoi.
5. Nhap mat khau tam thoi vao o moi va bam `Xac nhan va dang nhap`; neu dung thi he thong dang nhap user.
6. Nhap email sai dinh dang: form phai bao `Email không hợp lệ`.

### 3. Cai va cau hinh PHPMailer

#### Da lam

- Them dependency `phpmailer/phpmailer` vao `composer.json:5-9`.
- Da chay Composer local bang `composer.phar` de cai PHPMailer, tao `composer.lock` va `vendor/autoload.php`.
- Cap nhat `public/index.php` de load `vendor/autoload.php` neu da cai Composer dependencies: `public/index.php:30-35`.
- Hoan thien `MailService`:
  - Doc cau hinh tu `config/mail.php`: `app/Services/MailService.php:13-16`.
  - Ho tro che do SMTP bang PHPMailer: `app/Services/MailService.php:24-60`.
  - Dat `CharSet = 'UTF-8'`: `app/Services/MailService.php:37`.
  - Gui email text/plain: `app/Services/MailService.php:51-54`.
  - Bao loi ro neu PHPMailer chua duoc cai: `app/Services/MailService.php:24-26`.
  - Co fallback `log` de demo khi chua cau hinh SMTP that: `app/Services/MailService.php:62-85`.
- Cap nhat `config/mail.php` dung bien moi truong `MAIL_*`, them `mailer`, `log_path` va giu cac bien SMTP: `config/mail.php:3-13`.
- Them `.env.example` de cau hinh SMTP/App Password an toan, khong can ghi mat khau vao `config/mail.php`.

#### Ghi chu moi truong

- May hien tai khong co lenh `composer` trong PATH, nen da tai Composer dang `composer.phar` vao thu muc tam va chay:

```bash
php %TEMP%\composer.phar require phpmailer/phpmailer
```

- `composer.lock` da duoc tao, `vendor/` da cai package nhung bi `.gitignore` theo quy uoc project.
- De dung SMTP that, dat bien moi truong hoac sua `config/mail.php`:
  - `MAIL_MAILER=smtp`
  - `MAIL_HOST=smtp.gmail.com` hoac host SMTP cua nha cung cap
  - `MAIL_PORT=587`
  - `MAIL_USERNAME=email gui`
  - `MAIL_PASSWORD=app password/mat khau SMTP`
  - `MAIL_ENCRYPTION=tls`
  - `MAIL_FROM_EMAIL=email gui`
  - `MAIL_FROM_NAME=LobiBus`
- Khong commit mat khau SMTP that neu repo public.

#### Cach kiem chung

1. Kiem tra PHPMailer load duoc qua `vendor/autoload.php`.
2. De `MAIL_MAILER=log` hoac mac dinh `log`, gui quen mat khau cho user active, kiem tra `public/uploads/mail.log` co email va mat khau tam thoi.
3. Doi sang `MAIL_MAILER=smtp`, dien host/port/user/password va gui quen mat khau lai.
4. Email that phai den hop thu cua user; neu loi SMTP, form phai hien thong bao khong the gui email.

### 4. Gia co bao mat session cookie

#### Da lam

- Cap nhat `Session::start()` de cau hinh cookie truoc khi `session_start()`:
  - `lifetime => 0`: `app/Core/Session.php:19`.
  - `secure => true` khi HTTPS: `app/Core/Session.php:22`, `app/Core/Session.php:80-84`.
  - `httponly => true`: `app/Core/Session.php:23`.
  - `samesite => 'Lax'`: `app/Core/Session.php:24`.
- Cap nhat `Session::destroy()`:
  - Chi destroy khi session dang active: `app/Core/Session.php:58-62`.
  - Xoa cookie session khi logout: `app/Core/Session.php:64-77`.
- `Auth::login()` van giu `session_regenerate_id(true)` sau khi dang nhap: `app/Core/Auth.php:9-15`.

#### Cach kiem chung

1. Dang nhap vao he thong.
2. Mo DevTools/Application/Cookies va kiem tra cookie session co `HttpOnly`, `SameSite=Lax`.
3. Neu chay HTTPS, cookie phai co `Secure`.
4. Bam dang xuat va refresh lai trang: user phai mat session, vao `/admin` phai bi chuyen ve login.

### 5. Chinh lai header/footer cho dep va dung base URL

#### Da lam

- Navbar:
  - Them `user-name` va `title` de ten user dai khong lam vo layout: `app/Views/layouts/navbar.php:22-30`.
  - Giu link bang `url()` cho trang chu, menu, auth va admin: `app/Views/layouts/navbar.php:7-19`, `app/Views/layouts/navbar.php:28-33`.
- Footer:
  - Sap xep thanh 3 cot: thong tin lien he, lien ket nhanh, ho tro khach hang: `app/Views/layouts/footer.php:3-31`.
  - Them link nhanh Dat ve, Lich su dat ve, Goi y chuyen, Ho tro: `app/Views/layouts/footer.php:15-22`.
  - Them link Dang nhap, Dang ky, Quen mat khau: `app/Views/layouts/footer.php:24-30`.
- Header cu:
  - Doi `href="/"` thanh `href="<?= url('/') ?>"`: `app/Views/layouts/header.php:3`.
- CSS:
  - Them style header gon, sticky, dong nhat mau LobiBus: `public/assets/css/index.css:701-750`.
  - Them style footer nen toi, link de doc: `public/assets/css/index.css:752-770`.
  - Them responsive cho tablet/mobile: `public/assets/css/index.css:773-805`.
  - Them hover dep cho link/nut trong trang dang nhap, dang ky, quen mat khau va panel nhap mat khau tam thoi: `public/assets/css/index.css:807-869`.

#### Cach kiem chung

1. Mo `http://localhost/lobibus-1/public/`, kiem tra logo/menu/nut auth nam cung hang tren desktop.
2. Dang nhap bang user co ten dai, navbar phai cat bang dau `...`, khong vo layout.
3. Thu tren mobile width nho: header khong tran ngang, nut auth tu wrap neu can.
4. Bam cac link footer khi project nam trong subfolder XAMPP: link phai dung base URL.

### 6. Kiem tra da chay sau khi sua

- Lint PHP thanh cong:
  - `app/Services/AuthService.php`
  - `app/Services/MailService.php`
  - `app/Controllers/AuthController.php`
  - `app/Models/User.php`
  - `app/Core/Session.php`
  - `public/index.php`
  - `app/Views/auth/login.php`
  - `app/Views/auth/register.php`
  - `app/Views/auth/forgot-password.php`
  - `app/Views/layouts/navbar.php`
  - `app/Views/layouts/footer.php`
  - `app/Views/layouts/header.php`
- `composer.json` doc duoc bang PowerShell `ConvertFrom-Json`.
- `composer validate --strict` thanh cong sau khi them `license`.
- PHPMailer load duoc bang `vendor/autoload.php`.
- Test nhanh `MailService` che do `log` thanh cong: tra ve `mail log ok`.
- Chua test end-to-end voi database/MySQL trong trinh duyet o luot nay; can chay XAMPP va lam theo `tests/auth-test.md`.

### 7. Lam lai kiem tra so dien thoai khi dang ky va responsive mobile

#### Da lam

- Them validate so dien thoai o backend truoc khi tao user: `app/Services/AuthService.php`.
- Bat buoc nhap so dien thoai khi dang ky; chap nhan so di dong Viet Nam dang `0912345678`, `+84912345678` hoac `84912345678`.
- Chuan hoa so co ma quoc gia `+84`/`84` ve dang bat dau bang `0` truoc khi luu vao bang `users`.
- Them input `type="tel"`, `inputmode`, `pattern`, `title` va dong huong dan cho o so dien thoai: `app/Views/auth/register.php`.
- Them CSS responsive cho trang auth tren mobile: form rong 100%, input/toi thieu 44px, button full width, link xep doc, header gon hon tren man hinh nho: `public/assets/css/index.css`.
- Cap nhat test case thu cong cho dang ky so dien thoai trong `tests/auth-test.md`.

#### Cach kiem chung

1. Mo `http://localhost/lobibus-1/public/register` tren desktop va mobile width nho.
2. Bo trong so dien thoai: trinh duyet phai yeu cau nhap hoac server bao `Vui long nhap so dien thoai`.
3. Nhap `12345` hoac `0212345678`: form phai chan gui hoac hien loi so dien thoai khong dung so di dong Viet Nam.
4. Nhap `0912345678` hoac `+84912345678`: dang ky thanh cong neu email chua ton tai va mat khau dung policy.
5. Kiem tra DB: so co `+84` phai duoc luu ve dang `0912345678`.
6. Thu tren dien thoai: form khong tran ngang, input de bam, nut dang ky full width, link `Da co tai khoan` va `Quay ve trang chu` xep doc.

