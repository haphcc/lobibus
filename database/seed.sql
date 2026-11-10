USE lobibus;

START TRANSACTION;

-- =========================================================
-- 1. ROLES
-- =========================================================
INSERT INTO roles (id, name) VALUES
(1, 'admin'),
(2, 'customer')
ON DUPLICATE KEY UPDATE
name = VALUES(name);

-- =========================================================
-- 2. USERS
-- =========================================================
INSERT INTO users (id, role_id, name, email, phone, password, status) VALUES
(1, 1, 'Admin LobiBus', 'admin@lobibus.local', '0900000000', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(2, 2, 'Nguyễn An', 'an.nguyen@lobibus.local', '0900000001', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(3, 2, 'Trần Bình', 'binh.tran@lobibus.local', '0900000002', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(4, 2, 'Lê Chi', 'chi.le@lobibus.local', '0900000003', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(5, 2, 'Phạm Dao', 'dao.pham@lobibus.local', '0900000004', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(6, 2, 'Vũ Giang', 'giang.vu@lobibus.local', '0900000005', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(7, 2, 'Hoàng Huy', 'huy.hoang@lobibus.local', '0900000006', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(8, 2, 'Đỗ Khoa', 'khoa.do@lobibus.local', '0900000007', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(9, 2, 'Bùi Lam', 'lam.bui@lobibus.local', '0900000008', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(10, 2, 'Mai Minh', 'minh.mai@lobibus.local', '0900000009', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(11, 2, 'Nhân viên Một', 'staff1@lobibus.local', '0900000010', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(12, 2, 'Nhân viên Hai', 'staff2@lobibus.local', '0900000011', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(13, 2, 'Tài xế A', 'driver.a@lobibus.local', '0900000012', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(14, 2, 'Tài xế B', 'driver.b@lobibus.local', '0900000013', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(15, 2, 'Quản lý Vận hành', 'manager@lobibus.local', '0900000014', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(16, 2, 'Hỗ trợ Một', 'support1@lobibus.local', '0900000015', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(17, 2, 'Hỗ trợ Hai', 'support2@lobibus.local', '0900000016', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(18, 2, 'Khách bị khóa', 'locked.customer@lobibus.local', '0900000017', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'locked'),
(19, 2, 'Khách VIP', 'vip.customer@lobibus.local', '0900000018', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active'),
(20, 2, 'Dev Seed', 'dev@lobibus.local', '0900000019', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active')
ON DUPLICATE KEY UPDATE
role_id = VALUES(role_id),
name = VALUES(name),
phone = VALUES(phone),
password = VALUES(password),
status = VALUES(status);

-- =========================================================
-- 3. LOCATIONS
-- =========================================================
INSERT INTO locations (id, name, province, address, latitude, longitude) VALUES
(1, 'Hà Nội', 'Hà Nội', 'Bến xe Mỹ Đình', 21.0285110, 105.8048170),
(2, 'Huế', 'Huế', 'Bến xe phía Nam Huế', 16.4637130, 107.5908660),
(3, 'Lai Châu', 'Lai Châu', 'Bến xe Lai Châu', 22.3862220, 103.4702630),
(4, 'Điện Biên', 'Điện Biên', 'Bến xe Điện Biên Phủ', 21.3860200, 103.0230100),
(5, 'Sơn La', 'Sơn La', 'Bến xe Sơn La', 21.3270340, 103.9141280),
(6, 'Lạng Sơn', 'Lạng Sơn', 'Bến xe Lạng Sơn', 21.8526400, 106.7610100),
(7, 'Quảng Ninh', 'Quảng Ninh', 'Bến xe Bãi Cháy', 20.9711970, 107.0448060),
(8, 'Thanh Hóa', 'Thanh Hóa', 'Bến xe phía Bắc Thanh Hóa', 19.8070000, 105.7760000),
(9, 'Nghệ An', 'Nghệ An', 'Bến xe Vinh', 18.6790000, 105.6810000),
(10, 'Hà Tĩnh', 'Hà Tĩnh', 'Bến xe Hà Tĩnh', 18.3550000, 105.8870000),
(11, 'Cao Bằng', 'Cao Bằng', 'Bến xe Cao Bằng', 22.6666300, 106.2639850),
(12, 'Tuyên Quang', 'Tuyên Quang', 'Bến xe Tuyên Quang', 21.7767240, 105.2280190),
(13, 'Lào Cai', 'Lào Cai', 'Bến xe Trung tâm Lào Cai', 22.4855560, 103.9706670),
(14, 'Thái Nguyên', 'Thái Nguyên', 'Bến xe Thái Nguyên', 21.5942200, 105.8481700),
(15, 'Phú Thọ', 'Phú Thọ', 'Bến xe Việt Trì', 21.3227400, 105.4015400),
(16, 'Bắc Ninh', 'Bắc Ninh', 'Bến xe Bắc Ninh', 21.1860000, 106.0760000),
(17, 'Hưng Yên', 'Hưng Yên', 'Bến xe Hưng Yên', 20.6460000, 106.0510000),
(18, 'Hải Phòng', 'Hải Phòng', 'Bến xe Cầu Rào', 20.8449110, 106.6880840),
(19, 'Ninh Bình', 'Ninh Bình', 'Bến xe Ninh Bình', 20.2530000, 105.9750000),
(20, 'Quảng Trị', 'Quảng Trị', 'Bến xe Đông Hà', 16.8162500, 107.1003100),
(21, 'Đà Nẵng', 'Đà Nẵng', 'Bến xe Trung tâm Đà Nẵng', 16.0544070, 108.2021670),
(22, 'Quảng Ngãi', 'Quảng Ngãi', 'Bến xe Quảng Ngãi', 15.1213870, 108.8044140),
(23, 'Gia Lai', 'Gia Lai', 'Bến xe Pleiku', 13.9718350, 108.0150790),
(24, 'Khánh Hòa', 'Khánh Hòa', 'Bến xe phía Nam Nha Trang', 12.2380000, 109.1960000),
(25, 'Lâm Đồng', 'Lâm Đồng', 'Bến xe Liên tỉnh Đà Lạt', 11.9400000, 108.4580000),
(26, 'Đắk Lắk', 'Đắk Lắk', 'Bến xe phía Bắc Buôn Ma Thuột', 12.6661940, 108.0382470),
(27, 'Hồ Chí Minh', 'Hồ Chí Minh', 'Bến xe Miền Đông', 10.8230990, 106.6296640),
(28, 'Đồng Nai', 'Đồng Nai', 'Bến xe Biên Hòa', 10.9574130, 106.8426870),
(29, 'Tây Ninh', 'Tây Ninh', 'Bến xe Tây Ninh', 11.3351550, 106.1098850),
(30, 'Cần Thơ', 'Cần Thơ', 'Bến xe Cần Thơ', 10.0450000, 105.7460000),
(31, 'Vĩnh Long', 'Vĩnh Long', 'Bến xe Vĩnh Long', 10.2537000, 105.9722000),
(32, 'Đồng Tháp', 'Đồng Tháp', 'Bến xe Cao Lãnh', 10.4937980, 105.6881780),
(33, 'Cà Mau', 'Cà Mau', 'Bến xe Cà Mau', 9.1768200, 105.1524200),
(34, 'An Giang', 'An Giang', 'Bến xe Long Xuyên', 10.3863900, 105.4351800)
ON DUPLICATE KEY UPDATE
name = VALUES(name),
province = VALUES(province),
address = VALUES(address),
latitude = VALUES(latitude),
longitude = VALUES(longitude);

-- =========================================================
-- 4. ROUTES
-- =========================================================
INSERT INTO routes (id, from_location_id, to_location_id, distance_km, duration_minutes, status) VALUES
(1, 1, 2, 120.00, 150, 'active'),
(2, 1, 3, 90.00, 120, 'active'),
(3, 1, 4, 95.00, 130, 'active'),
(4, 1, 5, 160.00, 210, 'active'),
(5, 1, 6, 155.00, 190, 'active'),
(6, 1, 7, 35.00, 55, 'active'),
(7, 1, 8, 60.00, 90, 'active'),
(8, 1, 9, 110.00, 150, 'active'),
(9, 2, 6, 70.00, 95, 'active'),
(10, 3, 4, 50.00, 75, 'active'),
(11, 4, 5, 65.00, 90, 'active'),
(12, 5, 10, 145.00, 200, 'active'),
(13, 10, 11, 55.00, 80, 'active'),
(14, 11, 12, 360.00, 420, 'active'),
(15, 12, 13, 105.00, 150, 'active'),
(16, 12, 14, 320.00, 390, 'active'),
(17, 14, 15, 215.00, 270, 'active'),
(18, 15, 16, 140.00, 210, 'active'),
(19, 16, 17, 310.00, 420, 'active'),
(20, 17, 20, 170.00, 220, 'active')
ON DUPLICATE KEY UPDATE
from_location_id = VALUES(from_location_id),
to_location_id = VALUES(to_location_id),
distance_km = VALUES(distance_km),
duration_minutes = VALUES(duration_minutes),
status = VALUES(status);

-- Tạo đầy đủ tuyến hai chiều giữa 34 tỉnh/thành hiện hành.
-- route id dùng quy ước: from_location_id * 1000 + to_location_id.
INSERT INTO routes (id, from_location_id, to_location_id, distance_km, duration_minutes, status)
SELECT
    l1.id * 1000 + l2.id AS id,
    l1.id AS from_location_id,
    l2.id AS to_location_id,
    ROUND(
        111.045 * DEGREES(
            ACOS(
                LEAST(
                    1,
                    COS(RADIANS(l1.latitude)) * COS(RADIANS(l2.latitude)) *
                    COS(RADIANS(l2.longitude) - RADIANS(l1.longitude)) +
                    SIN(RADIANS(l1.latitude)) * SIN(RADIANS(l2.latitude))
                )
            )
        ),
        2
    ) AS distance_km,
    GREATEST(
        45,
        CEIL(
            (
                111.045 * DEGREES(
                    ACOS(
                        LEAST(
                            1,
                            COS(RADIANS(l1.latitude)) * COS(RADIANS(l2.latitude)) *
                            COS(RADIANS(l2.longitude) - RADIANS(l1.longitude)) +
                            SIN(RADIANS(l1.latitude)) * SIN(RADIANS(l2.latitude))
                        )
                    )
                )
            ) / 55 * 60
        ) + 30
    ) AS duration_minutes,
    'active' AS status
FROM locations l1
JOIN locations l2 ON l1.id <> l2.id
WHERE l1.id BETWEEN 1 AND 34
AND l2.id BETWEEN 1 AND 34
ON DUPLICATE KEY UPDATE
from_location_id = VALUES(from_location_id),
to_location_id = VALUES(to_location_id),
distance_km = VALUES(distance_km),
duration_minutes = VALUES(duration_minutes),
status = VALUES(status);

-- =========================================================
-- 5. BUSES
-- Bus 1-5 là xe chính có đủ ghế để chọn chỗ.
-- Bus 6-20 là xe dự phòng.
-- =========================================================
INSERT INTO buses (id, name, license_plate, bus_type, total_seats, image, status) VALUES
(1, 'LobiBus Standard 01', '29B-10001', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'active'),
(2, 'LobiBus Sleeper 01', '29B-10002', 'sleeper', 40, '/assets/images/bus/giuong-nam.png', 'active'),
(3, 'LobiBus Limousine 01', '29B-10003', 'limousine', 9, '/assets/images/bus/limousine.jpg', 'active'),
(4, 'LobiBus Standard 02', '29B-10004', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'active'),
(5, 'LobiBus Sleeper 02', '29B-10005', 'sleeper', 40, '/assets/images/bus/giuong-nam.png', 'active'),
(6, 'LobiBus Reserve 06', '29B-10006', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'inactive'),
(7, 'LobiBus Reserve 07', '29B-10007', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'inactive'),
(8, 'LobiBus Reserve 08', '29B-10008', 'sleeper', 40, '/assets/images/bus/giuong-nam.png', 'inactive'),
(9, 'LobiBus Reserve 09', '29B-10009', 'limousine', 9, '/assets/images/bus/limousine.jpg', 'inactive'),
(10, 'LobiBus Reserve 10', '29B-10010', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'inactive'),
(11, 'LobiBus Reserve 11', '30A-10011', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'inactive'),
(12, 'LobiBus Reserve 12', '30A-10012', 'sleeper', 40, '/assets/images/bus/giuong-nam.png', 'inactive'),
(13, 'LobiBus Reserve 13', '30A-10013', 'limousine', 9, '/assets/images/bus/limousine.jpg', 'inactive'),
(14, 'LobiBus Reserve 14', '30A-10014', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'inactive'),
(15, 'LobiBus Reserve 15', '30A-10015', 'sleeper', 40, '/assets/images/bus/giuong-nam.png', 'inactive'),
(16, 'LobiBus Reserve 16', '30A-10016', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'maintenance'),
(17, 'LobiBus Reserve 17', '30A-10017', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'inactive'),
(18, 'LobiBus Reserve 18', '30A-10018', 'limousine', 9, '/assets/images/bus/limousine.jpg', 'inactive'),
(19, 'LobiBus Reserve 19', '30A-10019', 'sleeper', 40, '/assets/images/bus/giuong-nam.png', 'inactive'),
(20, 'LobiBus Reserve 20', '31B-10020', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'inactive')
ON DUPLICATE KEY UPDATE
name = VALUES(name),
license_plate = VALUES(license_plate),
bus_type = VALUES(bus_type),
total_seats = VALUES(total_seats),
image = VALUES(image),
status = VALUES(status);

-- Thêm xe vận hành cho lịch toàn quốc tháng 6/2026.
INSERT INTO buses (id, name, license_plate, bus_type, total_seats, image, status)
SELECT
    n.id,
    CONCAT('LobiBus Fleet ', LPAD(n.id, 3, '0')) AS name,
    CONCAT('LB-', LPAD(n.id, 5, '0')) AS license_plate,
    CASE
        WHEN MOD(n.id, 3) = 0 THEN 'limousine'
        WHEN MOD(n.id, 3) = 1 THEN 'standard'
        ELSE 'sleeper'
    END AS bus_type,
    CASE
        WHEN MOD(n.id, 3) = 0 THEN 9
        WHEN MOD(n.id, 3) = 1 THEN 32
        ELSE 40
    END AS total_seats,
    CASE
        WHEN MOD(n.id, 3) = 0 THEN '/assets/images/bus/limousine.jpg'
        WHEN MOD(n.id, 3) = 1 THEN '/assets/images/bus/ghe-ngoi.jpg'
        ELSE '/assets/images/bus/giuong-nam.png'
    END AS image,
    'active' AS status
FROM (
    SELECT 21 id UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25
    UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL SELECT 30
    UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35
    UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL SELECT 40
    UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45
    UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL SELECT 50
    UNION ALL SELECT 51 UNION ALL SELECT 52 UNION ALL SELECT 53 UNION ALL SELECT 54 UNION ALL SELECT 55
    UNION ALL SELECT 56 UNION ALL SELECT 57 UNION ALL SELECT 58 UNION ALL SELECT 59 UNION ALL SELECT 60
    UNION ALL SELECT 61 UNION ALL SELECT 62 UNION ALL SELECT 63 UNION ALL SELECT 64 UNION ALL SELECT 65
    UNION ALL SELECT 66 UNION ALL SELECT 67 UNION ALL SELECT 68 UNION ALL SELECT 69 UNION ALL SELECT 70
    UNION ALL SELECT 71 UNION ALL SELECT 72 UNION ALL SELECT 73 UNION ALL SELECT 74 UNION ALL SELECT 75
    UNION ALL SELECT 76 UNION ALL SELECT 77 UNION ALL SELECT 78 UNION ALL SELECT 79 UNION ALL SELECT 80
    UNION ALL SELECT 81 UNION ALL SELECT 82 UNION ALL SELECT 83 UNION ALL SELECT 84 UNION ALL SELECT 85
    UNION ALL SELECT 86 UNION ALL SELECT 87 UNION ALL SELECT 88 UNION ALL SELECT 89 UNION ALL SELECT 90
    UNION ALL SELECT 91 UNION ALL SELECT 92 UNION ALL SELECT 93 UNION ALL SELECT 94 UNION ALL SELECT 95
    UNION ALL SELECT 96 UNION ALL SELECT 97 UNION ALL SELECT 98 UNION ALL SELECT 99 UNION ALL SELECT 100
    UNION ALL SELECT 101 UNION ALL SELECT 102 UNION ALL SELECT 103 UNION ALL SELECT 104 UNION ALL SELECT 105
    UNION ALL SELECT 106 UNION ALL SELECT 107 UNION ALL SELECT 108 UNION ALL SELECT 109 UNION ALL SELECT 110
    UNION ALL SELECT 111 UNION ALL SELECT 112 UNION ALL SELECT 113 UNION ALL SELECT 114 UNION ALL SELECT 115
    UNION ALL SELECT 116 UNION ALL SELECT 117 UNION ALL SELECT 118 UNION ALL SELECT 119 UNION ALL SELECT 120
) n
ON DUPLICATE KEY UPDATE
name = VALUES(name),
license_plate = VALUES(license_plate),
bus_type = VALUES(bus_type),
total_seats = VALUES(total_seats),
image = VALUES(image),
status = VALUES(status);

-- =========================================================
-- 6. SEATS
-- Seed đủ ghế cho tất cả xe đã tạo.
-- ID ghế dùng quy ước: bus_id * 1000 + số thứ tự ghế.
-- =========================================================
INSERT INTO seats (id, bus_id, seat_number, seat_type)
SELECT
    b.id * 1000 + n.n AS id,
    b.id AS bus_id,
    CASE
        WHEN b.id = 1 THEN CONCAT('A', LPAD(n.n, 2, '0'))
        WHEN b.id = 2 THEN CONCAT('B', LPAD(n.n, 2, '0'))
        WHEN b.id = 3 THEN CONCAT('VIP', LPAD(n.n, 2, '0'))
        WHEN b.id = 4 THEN CONCAT('C', LPAD(n.n, 2, '0'))
        WHEN b.id = 5 THEN CONCAT('D', LPAD(n.n, 2, '0'))
        WHEN b.bus_type = 'limousine' THEN CONCAT('VIP', LPAD(n.n, 2, '0'))
        WHEN b.bus_type = 'sleeper' THEN CONCAT('S', LPAD(n.n, 2, '0'))
        ELSE CONCAT('G', LPAD(n.n, 2, '0'))
    END AS seat_number,
    CASE
        WHEN b.bus_type = 'sleeper' THEN 'sleeper'
        WHEN b.bus_type = 'limousine' THEN 'vip'
        ELSE 'standard'
    END AS seat_type
FROM buses b
JOIN (
    SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
    UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
    UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
    UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20
    UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25
    UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL SELECT 30
    UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35
    UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL SELECT 40
) n
WHERE b.id BETWEEN 1 AND 120
AND n.n <= b.total_seats
ON DUPLICATE KEY UPDATE
bus_id = VALUES(bus_id),
seat_number = VALUES(seat_number),
seat_type = VALUES(seat_type);

-- =========================================================
-- 7. TRIPS
-- 20 chuyến dùng lặp 5 xe chính.
-- Không để cùng 1 xe chạy trùng thời gian trong cùng ngày.
-- =========================================================
INSERT INTO trips (id, route_id, bus_id, departure_time, arrival_time, price, available_seats, status) VALUES
(1, 1, 1, '2026-05-20 08:00:00', '2026-05-20 10:30:00', 150000.00, 31, 'scheduled'),
(2, 2, 2, '2026-05-20 08:30:00', '2026-05-20 10:30:00', 140000.00, 39, 'scheduled'),
(3, 3, 3, '2026-05-20 09:00:00', '2026-05-20 11:10:00', 180000.00, 8, 'scheduled'),
(4, 4, 4, '2026-05-20 09:30:00', '2026-05-20 13:00:00', 220000.00, 31, 'scheduled'),
(5, 5, 5, '2026-05-20 10:00:00', '2026-05-20 13:10:00', 165000.00, 39, 'scheduled'),

(6, 6, 1, '2026-05-21 08:00:00', '2026-05-21 09:05:00', 80000.00, 31, 'scheduled'),
(7, 7, 2, '2026-05-21 08:30:00', '2026-05-21 10:00:00', 95000.00, 39, 'scheduled'),
(8, 8, 3, '2026-05-21 09:00:00', '2026-05-21 11:30:00', 155000.00, 8, 'scheduled'),
(9, 9, 4, '2026-05-21 09:30:00', '2026-05-21 11:05:00', 110000.00, 31, 'scheduled'),
(10, 10, 5, '2026-05-21 10:00:00', '2026-05-21 11:15:00', 90000.00, 40, 'scheduled'),

(11, 11, 1, '2026-05-22 08:00:00', '2026-05-22 09:30:00', 100000.00, 31, 'scheduled'),
(12, 12, 2, '2026-05-22 08:30:00', '2026-05-22 11:50:00', 170000.00, 39, 'scheduled'),
(13, 13, 3, '2026-05-22 09:00:00', '2026-05-22 10:20:00', 95000.00, 8, 'scheduled'),
(14, 14, 4, '2026-05-22 09:30:00', '2026-05-22 16:30:00', 320000.00, 31, 'scheduled'),
(15, 15, 5, '2026-05-22 10:00:00', '2026-05-22 12:30:00', 185000.00, 39, 'scheduled'),

(16, 16, 1, '2026-05-23 08:00:00', '2026-05-23 14:30:00', 350000.00, 31, 'scheduled'),
(17, 17, 2, '2026-05-23 08:30:00', '2026-05-23 13:00:00', 260000.00, 40, 'scheduled'),
(18, 18, 3, '2026-05-23 09:00:00', '2026-05-23 12:30:00', 210000.00, 8, 'scheduled'),
(19, 19, 4, '2026-05-23 09:30:00', '2026-05-23 16:30:00', 300000.00, 31, 'completed'),
(20, 20, 5, '2026-05-23 10:00:00', '2026-05-23 13:40:00', 190000.00, 39, 'scheduled')
ON DUPLICATE KEY UPDATE
route_id = VALUES(route_id),
bus_id = VALUES(bus_id),
departure_time = VALUES(departure_time),
arrival_time = VALUES(arrival_time),
price = VALUES(price),
available_seats = VALUES(available_seats),
status = VALUES(status);

-- Sinh chuyến cho mọi tuyến hai chiều trong tất cả các ngày tháng 6/2026.
-- Mỗi tuyến có 1 chuyến/ngày, giờ chạy được phân bổ giữa sáng, chiều, tối.
-- trip id dùng quy ước: 1000000 + ngày trong tháng * 100000 + route_id.
DELETE FROM trips
WHERE id >= 1000000;

INSERT INTO trips (id, route_id, bus_id, departure_time, arrival_time, price, available_seats, status)
SELECT
    1000000 + DAY(d.service_date) * 100000 + r.id AS id,
    r.id AS route_id,
    b.id AS bus_id,
    TIMESTAMP(
        d.service_date,
        CASE MOD(r.id + DAY(d.service_date), 3)
            WHEN 0 THEN TIME('07:00:00')
            WHEN 1 THEN TIME('14:00:00')
            ELSE TIME('20:00:00')
        END
    ) AS departure_time,
    DATE_ADD(
        TIMESTAMP(
            d.service_date,
            CASE MOD(r.id + DAY(d.service_date), 3)
                WHEN 0 THEN TIME('07:00:00')
                WHEN 1 THEN TIME('14:00:00')
                ELSE TIME('20:00:00')
            END
        ),
        INTERVAL r.duration_minutes MINUTE
    ) AS arrival_time,
    GREATEST(70000, ROUND(r.distance_km * 1200 / 1000, 0) * 1000) AS price,
    b.total_seats AS available_seats,
    'scheduled' AS status
FROM routes r
JOIN (
    SELECT DATE('2026-06-01') service_date UNION ALL SELECT DATE('2026-06-02')
    UNION ALL SELECT DATE('2026-06-03') UNION ALL SELECT DATE('2026-06-04')
    UNION ALL SELECT DATE('2026-06-05') UNION ALL SELECT DATE('2026-06-06')
    UNION ALL SELECT DATE('2026-06-07') UNION ALL SELECT DATE('2026-06-08')
    UNION ALL SELECT DATE('2026-06-09') UNION ALL SELECT DATE('2026-06-10')
    UNION ALL SELECT DATE('2026-06-11') UNION ALL SELECT DATE('2026-06-12')
    UNION ALL SELECT DATE('2026-06-13') UNION ALL SELECT DATE('2026-06-14')
    UNION ALL SELECT DATE('2026-06-15') UNION ALL SELECT DATE('2026-06-16')
    UNION ALL SELECT DATE('2026-06-17') UNION ALL SELECT DATE('2026-06-18')
    UNION ALL SELECT DATE('2026-06-19') UNION ALL SELECT DATE('2026-06-20')
    UNION ALL SELECT DATE('2026-06-21') UNION ALL SELECT DATE('2026-06-22')
    UNION ALL SELECT DATE('2026-06-23') UNION ALL SELECT DATE('2026-06-24')
    UNION ALL SELECT DATE('2026-06-25') UNION ALL SELECT DATE('2026-06-26')
    UNION ALL SELECT DATE('2026-06-27') UNION ALL SELECT DATE('2026-06-28')
    UNION ALL SELECT DATE('2026-06-29') UNION ALL SELECT DATE('2026-06-30')
) d
JOIN buses b ON b.id = 21 + MOD(r.id + DAY(d.service_date), 100)
WHERE r.from_location_id BETWEEN 1 AND 34
AND r.to_location_id BETWEEN 1 AND 34
AND r.from_location_id <> r.to_location_id
AND r.id >= 1000
ON DUPLICATE KEY UPDATE
route_id = VALUES(route_id),
bus_id = VALUES(bus_id),
departure_time = VALUES(departure_time),
arrival_time = VALUES(arrival_time),
price = VALUES(price),
available_seats = VALUES(available_seats),
status = VALUES(status);

-- =========================================================
-- 8. BOOKINGS
-- =========================================================
INSERT INTO bookings (id, user_id, trip_id, booking_code, customer_name, customer_phone, customer_email, total_amount, status) VALUES
(1, 2, 1, 'LB-20260520-0001', 'Nguyễn An', '0911000001', 'an.nguyen@lobibus.local', 150000.00, 'confirmed'),
(2, 3, 2, 'LB-20260520-0002', 'Trần Bình', '0911000002', 'binh.tran@lobibus.local', 140000.00, 'pending'),
(3, 4, 3, 'LB-20260520-0003', 'Lê Chi', '0911000003', 'chi.le@lobibus.local', 180000.00, 'confirmed'),
(4, 5, 4, 'LB-20260520-0004', 'Phạm Dao', '0911000004', 'dao.pham@lobibus.local', 220000.00, 'confirmed'),
(5, 6, 5, 'LB-20260520-0005', 'Vũ Giang', '0911000005', 'giang.vu@lobibus.local', 165000.00, 'pending'),
(6, 7, 6, 'LB-20260521-0006', 'Hoàng Huy', '0911000006', 'huy.hoang@lobibus.local', 80000.00, 'confirmed'),
(7, 8, 7, 'LB-20260521-0007', 'Đỗ Khoa', '0911000007', 'khoa.do@lobibus.local', 95000.00, 'confirmed'),
(8, 9, 8, 'LB-20260521-0008', 'Bùi Lam', '0911000008', 'lam.bui@lobibus.local', 155000.00, 'pending'),
(9, 10, 9, 'LB-20260521-0009', 'Mai Minh', '0911000009', 'minh.mai@lobibus.local', 110000.00, 'confirmed'),
(10, 11, 10, 'LB-20260521-0010', 'Nhân viên Một', '0911000010', 'staff1@lobibus.local', 90000.00, 'cancelled'),
(11, 12, 11, 'LB-20260522-0011', 'Nhân viên Hai', '0911000011', 'staff2@lobibus.local', 100000.00, 'pending'),
(12, 13, 12, 'LB-20260522-0012', 'Tài xế A', '0911000012', 'driver.a@lobibus.local', 170000.00, 'confirmed'),
(13, 14, 13, 'LB-20260522-0013', 'Tài xế B', '0911000013', 'driver.b@lobibus.local', 95000.00, 'confirmed'),
(14, 15, 14, 'LB-20260522-0014', 'Quản lý Vận hành', '0911000014', 'manager@lobibus.local', 320000.00, 'completed'),
(15, 16, 15, 'LB-20260522-0015', 'Hỗ trợ Một', '0911000015', 'support1@lobibus.local', 185000.00, 'pending'),
(16, 17, 16, 'LB-20260523-0016', 'Hỗ trợ Hai', '0911000016', 'support2@lobibus.local', 350000.00, 'confirmed'),
(17, 18, 17, 'LB-20260523-0017', 'Khách bị khóa', '0911000017', 'locked.customer@lobibus.local', 260000.00, 'cancelled'),
(18, 19, 18, 'LB-20260523-0018', 'Khách VIP', '0911000018', 'vip.customer@lobibus.local', 210000.00, 'confirmed'),
(19, NULL, 19, 'LB-20260523-0019', 'Khách vãng lai 1', '0911000019', 'guest1@lobibus.local', 300000.00, 'completed'),
(20, NULL, 20, 'LB-20260523-0020', 'Khách vãng lai 2', '0911000020', 'guest2@lobibus.local', 190000.00, 'confirmed')
ON DUPLICATE KEY UPDATE
user_id = VALUES(user_id),
trip_id = VALUES(trip_id),
customer_name = VALUES(customer_name),
customer_phone = VALUES(customer_phone),
customer_email = VALUES(customer_email),
total_amount = VALUES(total_amount),
status = VALUES(status);

-- =========================================================
-- 9. BOOKING_DETAILS
-- booking_details đã có trip_id.
-- seat_id luôn thuộc đúng bus_id của trip.
-- =========================================================
INSERT INTO booking_details (id, booking_id, trip_id, seat_id, price) VALUES
(1, 1, 1, 1001, 150000.00),
(2, 2, 2, 2001, 140000.00),
(3, 3, 3, 3001, 180000.00),
(4, 4, 4, 4001, 220000.00),
(5, 5, 5, 5001, 165000.00),

(6, 6, 6, 1002, 80000.00),
(7, 7, 7, 2002, 95000.00),
(8, 8, 8, 3002, 155000.00),
(9, 9, 9, 4002, 110000.00),
(10, 10, 10, 5002, 90000.00),

(11, 11, 11, 1003, 100000.00),
(12, 12, 12, 2003, 170000.00),
(13, 13, 13, 3003, 95000.00),
(14, 14, 14, 4003, 320000.00),
(15, 15, 15, 5003, 185000.00),

(16, 16, 16, 1004, 350000.00),
(17, 17, 17, 2004, 260000.00),
(18, 18, 18, 3004, 210000.00),
(19, 19, 19, 4004, 300000.00),
(20, 20, 20, 5004, 190000.00)
ON DUPLICATE KEY UPDATE
booking_id = VALUES(booking_id),
trip_id = VALUES(trip_id),
seat_id = VALUES(seat_id),
price = VALUES(price);

-- =========================================================
-- 10. TICKETS
-- =========================================================
INSERT INTO tickets (id, booking_id, ticket_code, qr_code_path, status) VALUES
(1, 1, 'TICK-20260520-0001', '/assets/qrs/tick-0001.png', 'valid'),
(2, 2, 'TICK-20260520-0002', '/assets/qrs/tick-0002.png', 'valid'),
(3, 3, 'TICK-20260520-0003', '/assets/qrs/tick-0003.png', 'valid'),
(4, 4, 'TICK-20260520-0004', '/assets/qrs/tick-0004.png', 'valid'),
(5, 5, 'TICK-20260520-0005', '/assets/qrs/tick-0005.png', 'valid'),
(6, 6, 'TICK-20260521-0006', '/assets/qrs/tick-0006.png', 'valid'),
(7, 7, 'TICK-20260521-0007', '/assets/qrs/tick-0007.png', 'valid'),
(8, 8, 'TICK-20260521-0008', '/assets/qrs/tick-0008.png', 'valid'),
(9, 9, 'TICK-20260521-0009', '/assets/qrs/tick-0009.png', 'valid'),
(10, 10, 'TICK-20260521-0010', '/assets/qrs/tick-0010.png', 'cancelled'),
(11, 11, 'TICK-20260522-0011', '/assets/qrs/tick-0011.png', 'valid'),
(12, 12, 'TICK-20260522-0012', '/assets/qrs/tick-0012.png', 'valid'),
(13, 13, 'TICK-20260522-0013', '/assets/qrs/tick-0013.png', 'valid'),
(14, 14, 'TICK-20260522-0014', '/assets/qrs/tick-0014.png', 'used'),
(15, 15, 'TICK-20260522-0015', '/assets/qrs/tick-0015.png', 'valid'),
(16, 16, 'TICK-20260523-0016', '/assets/qrs/tick-0016.png', 'valid'),
(17, 17, 'TICK-20260523-0017', '/assets/qrs/tick-0017.png', 'cancelled'),
(18, 18, 'TICK-20260523-0018', '/assets/qrs/tick-0018.png', 'valid'),
(19, 19, 'TICK-20260523-0019', '/assets/qrs/tick-0019.png', 'used'),
(20, 20, 'TICK-20260523-0020', '/assets/qrs/tick-0020.png', 'valid')
ON DUPLICATE KEY UPDATE
booking_id = VALUES(booking_id),
qr_code_path = VALUES(qr_code_path),
status = VALUES(status);

-- =========================================================
-- 11. PAYMENTS
-- =========================================================
INSERT INTO payments (id, booking_id, method, amount, status, transaction_code) VALUES
(1, 1, 'momo', 150000.00, 'paid', 'TXN-0001-MOMO'),
(2, 2, 'bank_transfer', 140000.00, 'pending', 'TXN-0002-BANK'),
(3, 3, 'card', 180000.00, 'paid', 'TXN-0003-CARD'),
(4, 4, 'zalopay', 220000.00, 'paid', 'TXN-0004-ZALO'),
(5, 5, 'cash', 165000.00, 'pending', 'TXN-0005-CASH'),
(6, 6, 'cash', 80000.00, 'paid', 'TXN-0006-CASH'),
(7, 7, 'momo', 95000.00, 'paid', 'TXN-0007-MOMO'),
(8, 8, 'bank_transfer', 155000.00, 'failed', 'TXN-0008-BANK'),
(9, 9, 'card', 110000.00, 'paid', 'TXN-0009-CARD'),
(10, 10, 'cash', 90000.00, 'refunded', 'TXN-0010-CASH'),
(11, 11, 'zalopay', 100000.00, 'pending', 'TXN-0011-ZALO'),
(12, 12, 'momo', 170000.00, 'paid', 'TXN-0012-MOMO'),
(13, 13, 'bank_transfer', 95000.00, 'paid', 'TXN-0013-BANK'),
(14, 14, 'card', 320000.00, 'paid', 'TXN-0014-CARD'),
(15, 15, 'momo', 185000.00, 'pending', 'TXN-0015-MOMO'),
(16, 16, 'zalopay', 350000.00, 'paid', 'TXN-0016-ZALO'),
(17, 17, 'bank_transfer', 260000.00, 'refunded', 'TXN-0017-BANK'),
(18, 18, 'card', 210000.00, 'paid', 'TXN-0018-CARD'),
(19, 19, 'cash', 300000.00, 'paid', 'TXN-0019-CASH'),
(20, 20, 'momo', 190000.00, 'paid', 'TXN-0020-MOMO')
ON DUPLICATE KEY UPDATE
booking_id = VALUES(booking_id),
method = VALUES(method),
amount = VALUES(amount),
status = VALUES(status),
transaction_code = VALUES(transaction_code);

-- =========================================================
-- 12. REVIEWS
-- =========================================================
INSERT INTO reviews (id, user_id, trip_id, rating, comment) VALUES
(1, 2, 1, 5, 'Xe đúng giờ, sạch sẽ.'),
(2, 3, 2, 4, 'Chuyến đi ổn, nhân viên lịch sự.'),
(3, 4, 3, 5, 'Limousine thoải mái.'),
(4, 5, 4, 4, 'Đặt vé nhanh, xe chạy ổn.'),
(5, 6, 5, 3, 'Xe ổn nhưng hơi chậm.'),
(6, 7, 6, 5, 'Đi gần, phục vụ nhanh.'),
(7, 8, 7, 4, 'Đúng giờ.'),
(8, 9, 8, 2, 'Thanh toán chuyển khoản bị lỗi.'),
(9, 10, 9, 5, 'Tài xế chạy êm.'),
(10, 11, 10, 1, 'Vé đã bị hủy nên trải nghiệm chưa tốt.'),
(11, 12, 11, 3, 'Trải nghiệm trung bình.'),
(12, 13, 12, 5, 'Giường nằm thoải mái.'),
(13, 14, 13, 4, 'Ổn so với giá.'),
(14, 15, 14, 5, 'Chuyến dài nhưng khá dễ chịu.'),
(15, 16, 15, 4, 'Xe đẹp, ghế ổn.'),
(16, 17, 16, 5, 'Rất hài lòng.'),
(17, 18, 17, 1, 'Tôi đã hủy vé.'),
(18, 19, 18, 5, 'Dịch vụ VIP tốt.'),
(19, NULL, 19, 4, 'Khách vãng lai đánh giá tốt.'),
(20, NULL, 20, 5, 'Mua vé nhanh gọn.')
ON DUPLICATE KEY UPDATE
user_id = VALUES(user_id),
trip_id = VALUES(trip_id),
rating = VALUES(rating),
comment = VALUES(comment);

-- =========================================================
-- 13. CHATBOT QUESTIONS
-- =========================================================
INSERT INTO chatbot_questions (id, keyword, question, answer) VALUES
(1, 'hủy vé', 'Tôi có thể hủy vé không?', 'Bạn có thể hủy vé trong mục Lịch sử đặt vé nếu vé còn đủ điều kiện.'),
(2, 'thanh toán', 'LobiBus hỗ trợ thanh toán gì?', 'LobiBus hỗ trợ tiền mặt, chuyển khoản ngân hàng, MoMo, ZaloPay và thẻ.'),
(3, 'đổi ghế', 'Tôi muốn đổi ghế thì làm sao?', 'Bạn có thể đổi ghế trước giờ khởi hành nếu chuyến còn ghế trống.'),
(4, 'đặt vé', 'Làm sao để đặt vé?', 'Bạn chọn tuyến, chọn chuyến, chọn ghế, nhập thông tin và thanh toán.'),
(5, 'hoàn tiền', 'Bao lâu thì được hoàn tiền?', 'Thời gian hoàn tiền tùy phương thức thanh toán, thường từ 1 đến 7 ngày làm việc.'),
(6, 'mã đặt vé', 'Tôi quên mã đặt vé thì sao?', 'Bạn có thể tra cứu bằng số điện thoại hoặc email đã dùng khi đặt vé.'),
(7, 'checkin', 'Tôi check-in lên xe thế nào?', 'Bạn đưa mã vé hoặc mã QR cho nhân viên soát vé trước khi lên xe.'),
(8, 'hành lý', 'Tôi được mang bao nhiêu hành lý?', 'Thông thường mỗi khách được mang một kiện hành lý ký gửi và một túi xách tay.'),
(9, 'trễ giờ', 'Nếu tôi đến trễ thì sao?', 'Bạn nên đến trước giờ khởi hành. Nếu đến trễ, vé có thể không còn hiệu lực tùy quy định.'),
(10, 'trẻ em', 'Trẻ em có cần mua vé không?', 'Trẻ em có chỗ ngồi riêng thường cần mua vé. Quy định có thể thay đổi theo từng chuyến.'),
(11, 'hóa đơn', 'Có xuất hóa đơn không?', 'LobiBus có thể hỗ trợ xuất hóa đơn nếu bạn cung cấp đầy đủ thông tin.'),
(12, 'điểm đón', 'Tôi có thể chọn điểm đón không?', 'Nếu tuyến có hỗ trợ điểm đón, hệ thống sẽ hiển thị khi bạn đặt vé.'),
(13, 'điểm trả', 'Tôi có thể chọn điểm trả không?', 'Nếu tuyến có hỗ trợ điểm trả, hệ thống sẽ hiển thị khi bạn đặt vé.'),
(14, 'đổi giờ', 'Tôi muốn đổi giờ đi được không?', 'Bạn có thể đổi giờ trước giờ khởi hành nếu chuyến mới còn chỗ và vé đủ điều kiện.'),
(15, 'khuyến mãi', 'Có mã giảm giá không?', 'Mã giảm giá sẽ hiển thị ở trang thanh toán nếu đang có chương trình khuyến mãi.'),
(16, 'liên hệ', 'Liên hệ tổng đài như nào?', 'Bạn có thể liên hệ hotline, email hoặc fanpage hỗ trợ của LobiBus.'),
(17, 'bảo mật', 'Thông tin của tôi có được bảo mật không?', 'Thông tin của bạn được dùng để xử lý đặt vé và hỗ trợ khách hàng.'),
(18, 'đặt hộ', 'Tôi đặt hộ người khác được không?', 'Bạn có thể đặt hộ bằng cách nhập đúng thông tin hành khách khi đặt vé.'),
(19, 'đổi tuyến', 'Tôi muốn đổi tuyến thì sao?', 'Bạn có thể hủy vé hoặc đổi vé theo chính sách của từng chuyến.'),
(20, 'giờ chạy', 'Xem giờ chạy ở đâu?', 'Bạn có thể xem giờ chạy trong danh sách chuyến theo tuyến và ngày khởi hành.')
ON DUPLICATE KEY UPDATE
keyword = VALUES(keyword),
question = VALUES(question),
answer = VALUES(answer);

COMMIT;

-- =========================================================
-- CHECK 1: kiểm tra ghế có thuộc đúng xe của chuyến không
-- Nếu trả về 0 dòng là đúng.
-- =========================================================
SELECT 
    bd.id,
    bd.booking_id,
    bd.trip_id,
    t.bus_id AS trip_bus_id,
    bd.seat_id,
    s.bus_id AS seat_bus_id
FROM booking_details bd
JOIN trips t ON bd.trip_id = t.id
JOIN seats s ON bd.seat_id = s.id
WHERE t.bus_id <> s.bus_id;

-- =========================================================
-- CHECK 2: xem số ghế đã seed cho toàn bộ xe
-- =========================================================
SELECT 
    b.id AS bus_id,
    b.name,
    b.total_seats,
    COUNT(s.id) AS seeded_seats
FROM buses b
LEFT JOIN seats s ON s.bus_id = b.id
WHERE b.id BETWEEN 1 AND 120
GROUP BY b.id, b.name, b.total_seats;
