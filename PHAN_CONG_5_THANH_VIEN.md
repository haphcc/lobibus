# Phan cong cong viec cho 5 thanh vien

Tai lieu nay dua tren codebase hien tai sau migration sang PHP/MySQL MVC. Muc tieu la chia viec ro rang de moi nguoi lam song song, han che sua trung file va tranh dua lai code HTML/JS cu khong con dung.

## Trang thai codebase hien tai

- Entry point: `public/index.php`
- Core MVC: `app/Core/`
- Controllers: `app/Controllers/`
- Models: `app/Models/`
- Services: `app/Services/`
- Views: `app/Views/`
- Assets dang dung: `public/assets/`
- Database scripts: `database/schema.sql`, `database/seed.sql`
- HTML cu chi de tham khao: `legacy/`

Luu y quan trong:

- Khong tao lai file `.html` o root project.
- Khong dung lai JS cu theo kieu `localStorage` lam nghiep vu chinh.
- Neu can noi dung/giao dien cu, chi tham khao trong `legacy/`, sau do viet lai thanh PHP view va API moi.
- Tat ca route chay qua `public/index.php`.
- Khi them route moi, them trong `public/index.php`.
- Khi lam viec voi database, uu tien PDO trong Model/Service, khong query truc tiep trong View.

## Thanh vien 1: Core MVC, Auth, Session, phan quyen

### File phu trach chinh

- `app/Core/App.php`
- `app/Core/Router.php`
- `app/Core/Controller.php`
- `app/Core/Model.php`
- `app/Core/Database.php`
- `app/Core/Session.php`
- `app/Core/Auth.php`
- `app/Core/Helper.php`
- `app/Controllers/AuthController.php`
- `app/Controllers/HomeController.php`
- `app/Models/User.php`
- `app/Models/Role.php`
- `app/Services/AuthService.php`
- `app/Views/auth/login.php`
- `app/Views/auth/register.php`
- `app/Views/layouts/main.php`
- `app/Views/layouts/admin.php`
- `app/Views/layouts/navbar.php`
- `app/Views/layouts/sidebar.php`

### Viec can lam

1. Hoan thien `AuthService`:
   - Dang ky user moi.
   - Hash password bang `password_hash`.
   - Dang nhap bang `password_verify`.
   - Tra ve thong tin user va role.
2. Hoan thien `User` model:
   - `findByEmail`
   - `create`
   - `verifyLogin`
3. Hoan thien `Role` model:
   - Lay danh sach role.
   - Tim role theo ten neu can.
4. Lam middleware/check phan quyen:
   - Khach hang.
   - Admin.
   - Chan truy cap `/admin` neu chua dang nhap admin.
5. Sua layout:
   - Navbar hien thi dang nhap/dang xuat theo session.
   - Admin sidebar co link dung den cac module admin.
6. Kiem tra route khi chay bang XAMPP:
   - `http://localhost/lobibus-1/public/`
   - `http://localhost/lobibus-1/public/login`
   - `http://localhost/lobibus-1/public/admin`

### Luu y

- Khong sua nghiep vu booking, trip, payment tru khi can de test auth.
- Khong hard-code admin trong PHP, admin phai lay tu database.
- Neu doi cau truc session, bao lai cac thanh vien khac vi booking/admin co the can user id.

### Dau ra can ban giao

- Dang ky, dang nhap, dang xuat chay duoc.
- Admin route bi chan neu khong co quyen.
- Layout chung khong bi loi link khi chay trong subfolder XAMPP.

## Thanh vien 2: Giao dien khach hang, tim chuyen, ban do

### File phu trach chinh

- `app/Controllers/TripController.php`
- `app/Controllers/Api/TripApiController.php`
- `app/Models/Trip.php`
- `app/Models/Route.php`
- `app/Models/Location.php`
- `app/Views/home/index.php`
- `app/Views/trips/search.php`
- `app/Views/trips/list.php`
- `app/Views/trips/detail.php`
- `public/assets/js/trip-search.js`
- `public/assets/js/map.js`
- `public/assets/css/customer.css`

### Viec can lam

1. Hoan thien form tim chuyen:
   - Diem di.
   - Diem den.
   - Ngay khoi hanh.
   - So ghe/so hanh khach neu can.
2. Hoan thien API `/api/trips/search`:
   - Lay du lieu that tu bang `trips`, `routes`, `locations`, `buses`.
   - Loc theo diem di, diem den, ngay khoi hanh.
   - Chi hien thi chuyen `scheduled`.
3. Hoan thien `Trip` model:
   - `search`
   - `find`
   - `getAvailableSeats`
4. Hoan thien `Route` va `Location` model:
   - `all`
   - `find`
   - Cac ham phu neu can cho select box.
5. Hoan thien giao dien:
   - Trang chu co form tim chuyen.
   - Trang danh sach chuyen hien thi gia, gio di, gio den, xe, so ghe con.
   - Trang chi tiet chuyen co thong tin day du.
6. Tich hop Leaflet/OpenStreetMap trong `map.js`:
   - Hien thi diem di/diem den.
   - Co placeholder neu chua co toa do.

### Luu y

- Khong dua lai `routeData.js`; file do da bi xoa vi la data hard-code cu.
- Khong fetch den URL tuyet doi `/api/...`; dung `window.APP_BASE_URL` nhu cac JS hien tai.
- Neu can them cot toa do, trao doi voi thanh vien 4 de cap nhat `schema.sql`.

### Dau ra can ban giao

- Tim chuyen bang Fetch API khong reload trang.
- Ket qua tim kiem lay tu database.
- Trang chi tiet chuyen chay duoc.
- Ban do co the hien diem lien quan.

## Thanh vien 3: Dat ve, chon ghe, ticket, QR

### File phu trach chinh

- `app/Controllers/BookingController.php`
- `app/Controllers/TicketController.php`
- `app/Controllers/Api/SeatApiController.php`
- `app/Controllers/Api/BookingApiController.php`
- `app/Models/Booking.php`
- `app/Models/BookingDetail.php`
- `app/Models/Seat.php`
- `app/Models/Ticket.php`
- `app/Services/BookingService.php`
- `app/Services/TicketService.php`
- `app/Services/QRCodeService.php`
- `app/Views/bookings/select-seat.php`
- `app/Views/bookings/checkout.php`
- `app/Views/bookings/history.php`
- `app/Views/bookings/detail.php`
- `app/Views/tickets/qr.php`
- `public/assets/js/seat-selection.js`
- `public/assets/js/booking.js`
- `public/assets/qrcodes/`

### Viec can lam

1. Hoan thien API `/api/seats`:
   - Lay ghe theo trip.
   - Danh dau ghe da dat trong `booking_details`.
2. Hoan thien giao dien chon ghe:
   - Ghe trong.
   - Ghe da dat.
   - Ghe dang chon.
   - Tong tien tam tinh.
3. Hoan thien API `/api/bookings/create`:
   - Validate trip, ghe, thong tin khach.
   - Tao booking.
   - Tao booking details.
   - Tao ticket.
   - Tao payment pending neu can.
4. Hoan thien `BookingService`:
   - Dung transaction.
   - Chong dat trung ghe.
   - Tinh tong tien theo so ghe va gia trip.
5. Hoan thien lich su dat ve:
   - Lay booking theo user dang nhap.
   - Hien thi trang thai booking/ticket/payment.
6. Hoan thien huy ve:
   - Chi cho huy neu chuyen chua khoi hanh.
   - Cap nhat status booking/ticket/payment phu hop.
7. QR Code:
   - Tam thoi co `QRCodeService` skeleton.
   - Neu cai package duoc, ghi file QR vao `public/assets/qrcodes/`.

### Luu y

- Khong dung `localStorage` de luu booking chinh.
- Khong lay gia tien tu client lam gia tri tin cay. Client chi gui ghe/thong tin, server tinh tien.
- Moi thao tac tao booking phai nam trong transaction.
- Neu can bang/cot moi, trao doi thanh vien 4.

### Dau ra can ban giao

- Chon ghe truc quan.
- Dat ve tao du lieu that trong database.
- Xem lich su dat ve.
- Xem chi tiet ve va QR.

## Thanh vien 4: Admin, CRUD du lieu, database

### File phu trach chinh

- `database/schema.sql`
- `database/seed.sql`
- `app/Controllers/Admin/DashboardController.php`
- `app/Controllers/Admin/UserController.php`
- `app/Controllers/Admin/LocationController.php`
- `app/Controllers/Admin/RouteController.php`
- `app/Controllers/Admin/BusController.php`
- `app/Controllers/Admin/SeatController.php`
- `app/Controllers/Admin/TripController.php`
- `app/Controllers/Admin/BookingController.php`
- `app/Controllers/Admin/PaymentController.php`
- `app/Models/Location.php`
- `app/Models/Route.php`
- `app/Models/Bus.php`
- `app/Models/Seat.php`
- `app/Models/Trip.php`
- `app/Models/Booking.php`
- `app/Models/Payment.php`
- `app/Views/admin/`
- `public/assets/css/admin.css`

### Viec can lam

1. Ra soat database:
   - Kiem tra khoa chinh, khoa ngoai.
   - Kiem tra enum/status co du cho nghiep vu.
   - Them index neu can cho tim kiem trip va booking.
2. Hoan thien seed:
   - Du location.
   - Du route.
   - Du bus.
   - Du seat cho tung bus.
   - Du trip demo de cac thanh vien khac test.
3. Hoan thien admin CRUD:
   - Locations.
   - Routes.
   - Buses.
   - Seats.
   - Trips.
   - Bookings.
   - Payments.
4. Hoan thien admin dashboard:
   - Tong user.
   - Tong trip.
   - Tong booking.
   - Doanh thu.
5. Lam form admin:
   - Create.
   - Edit.
   - Delete/co confirm.
   - Update status.

### Luu y

- Khong thay doi schema tuy tien neu anh huong thanh vien 2/3/5. Neu doi cot, ghi lai trong README hoac comment commit.
- Khong xoa bang `chatbot_questions`; thanh vien 5 can dung.
- Admin controller hien moi skeleton, can bo sung route trong `public/index.php` cho tung action neu lam CRUD day du.

### Dau ra can ban giao

- Import duoc `schema.sql` va `seed.sql`.
- Admin quan ly du lieu chinh duoc.
- Co data demo du de test tim chuyen, dat ve, thanh toan.

## Thanh vien 5: Chatbot, goi y, thong ke, test, deploy

### File phu trach chinh

- `app/Controllers/ChatbotController.php`
- `app/Controllers/RecommendationController.php`
- `app/Controllers/Admin/StatisticController.php`
- `app/Controllers/Api/ChatbotApiController.php`
- `app/Controllers/Api/RecommendationApiController.php`
- `app/Models/Chatbot.php`
- `app/Models/Recommendation.php`
- `app/Models/Statistic.php`
- `app/Services/ChatbotService.php`
- `app/Services/RecommendationService.php`
- `app/Views/chatbot/widget.php`
- `app/Views/admin/statistics/index.php`
- `public/assets/js/chatbot.js`
- `public/assets/js/recommendation.js`
- `public/assets/js/dashboard-chart.js`
- `tests/`
- `README.md`

### Viec can lam

1. Chatbot:
   - Doc cau hoi/keyword/answer tu bang `chatbot_questions`.
   - Tra loi cac cau hoi co ban: dat ve, huy ve, xem ve, thanh toan.
   - Neu khong tim thay, tra loi mac dinh lich su.
2. Goi y chuyen:
   - Chuyen re nhat.
   - Chuyen khoi hanh som nhat.
   - Chuyen con nhieu ghe nhat.
   - Chuyen pho bien nhat neu co du lieu booking.
3. Thong ke:
   - Tong ve.
   - Tong doanh thu.
   - Tong chuyen xe.
   - Tong nguoi dung.
   - Doanh thu theo ngay/thang neu kip.
4. Dashboard chart:
   - Hoan thien `dashboard-chart.js`.
   - Co the dung Chart.js CDN neu can.
5. Kiem thu:
   - Cap nhat `tests/auth-test.md`.
   - Cap nhat `tests/booking-test.md`.
   - Cap nhat `tests/admin-test.md`.
   - Cap nhat `tests/test-cases.md`.
6. Deploy/demo:
   - Viet huong dan chay XAMPP/Laragon trong README.
   - Chup anh man hinh cac man hinh chinh.
   - Kiem tra lai tat ca route truoc khi nop.

### Luu y

- Khong viet chatbot hard-code qua nhieu trong JS. Nen de PHP/API xu ly.
- Goi y chuyen phai lay tu database, khong tao mang JS co dinh.
- Khi test, ghi ro bug nao da fix, bug nao con TODO.

### Dau ra can ban giao

- Chatbot goi API va tra loi duoc.
- Goi y chuyen co du lieu that.
- Trang thong ke admin co so lieu.
- Co test cases va huong dan demo ro rang.

## Quy uoc lam viec chung

### Khi them route

Them route trong `public/index.php`, vi hien project chua tach file `routes.php`.

Vi du:

```php
$router->get('/admin/trips', [App\Controllers\Admin\TripController::class, 'index']);
$router->post('/admin/trips/store', [App\Controllers\Admin\TripController::class, 'store']);
```

### Khi viet link trong View

Dung helper:

```php
href="<?= url('/trips/search') ?>"
src="<?= asset('images/logo.svg') ?>"
```

Khong viet cung `/lobibus-1/public/...` vi khi doi moi truong se hong.

### Khi fetch API trong JS

Dung:

```js
const base = window.APP_BASE_URL || '';
fetch(`${base}/api/trips/search`);
```

Khong dung truc tiep:

```js
fetch('/api/trips/search');
```

vi se loi khi chay trong subfolder XAMPP.

### Khi thao tac database

- Model/Service dung PDO.
- View khong duoc query database.
- Nghiep vu phuc tap dat trong Service.
- Booking/payment phai dung transaction.

### Khi sua giao dien

- CSS chinh cua khach hang: `public/assets/css/customer.css`
- CSS admin: `public/assets/css/admin.css`
- `index.css` va `datchuyen.css` la CSS cu con duoc giu lai de giao dien hien tai khong vo. Neu sua, kiem tra lai trang chu.

### Viec khong nen lam

- Khong khoi phuc lai `css/`, `js/`, `images/` o root.
- Khong tao lai HTML tinh o root.
- Khong luu booking bang `localStorage`.
- Khong de password plaintext.
- Khong sua file cua thanh vien khac neu chua thong nhat.

## Thu tu uu tien de ca nhom lam

1. Thanh vien 4 chay va on dinh database seed.
2. Thanh vien 1 hoan thien auth/session/admin guard.
3. Thanh vien 2 noi search trip voi database.
4. Thanh vien 3 noi booking/seat/ticket voi database.
5. Thanh vien 5 noi chatbot/recommendation/statistic va test toan he thong.

## Checklist truoc khi nop

- Trang chu mo duoc qua `http://localhost/lobibus-1/public/`.
- Khong con route 404 cho cac trang chinh.
- Dang ky/dang nhap/dang xuat hoat dong.
- Tim chuyen lay tu database.
- Chon ghe va dat ve ghi vao database.
- Admin CRUD du lieu chinh.
- Chatbot va goi y chuyen co API.
- `schema.sql` va `seed.sql` import duoc.
- README cap nhat cach chay va tai khoan demo.
- Anh chup man hinh day du cho bao cao.
