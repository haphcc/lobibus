USE lobibus;

INSERT INTO roles (id, name) VALUES
(1, 'admin'),
(2, 'customer')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (role_id, name, email, phone, password, status) VALUES
(1, 'Admin LobiBus', 'admin@lobibus.local', '0900000000', '$2y$10$8DB7XGjg1PBM3MgIUFQCyOc6ibuf4Sj.2R2rd0S.kvcLTdZMCYDQK', 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO locations (id, name, province, address) VALUES
(1, 'Hà Nội', 'Hà Nội', 'Bến xe Mỹ Đình'),
(2, 'Hải Phòng', 'Hải Phòng', 'Bến xe Cầu Rào'),
(3, 'Nam Định', 'Nam Định', 'Bến xe Nam Định'),
(4, 'Ninh Bình', 'Ninh Bình', 'Bến xe Ninh Bình'),
(5, 'Thanh Hóa', 'Thanh Hóa', 'Bến xe phía Bắc Thanh Hóa')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO routes (id, from_location_id, to_location_id, distance_km, duration_minutes, status) VALUES
(1, 1, 2, 120, 150, 'active'),
(2, 1, 3, 90, 120, 'active'),
(3, 1, 4, 95, 130, 'active'),
(4, 1, 5, 160, 210, 'active')
ON DUPLICATE KEY UPDATE distance_km = VALUES(distance_km);

INSERT INTO buses (id, name, license_plate, bus_type, total_seats, image, status) VALUES
(1, 'LobiBus Express 32', '29B-12345', 'standard', 32, '/assets/images/bus/ghe-ngoi.jpg', 'active'),
(2, 'LobiBus Sleeper', '29B-22345', 'sleeper', 40, '/assets/images/bus/giuong-nam.png', 'active'),
(3, 'LobiBus Limousine', '29B-32345', 'limousine', 9, '/assets/images/bus/limousine.jpg', 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO seats (bus_id, seat_number, seat_type)
SELECT 1, CONCAT('A', LPAD(n, 2, '0')), 'standard'
FROM (
    SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
    UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16
    UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24
    UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32
) numbers
ON DUPLICATE KEY UPDATE seat_type = VALUES(seat_type);

INSERT INTO trips (id, route_id, bus_id, departure_time, arrival_time, price, status) VALUES
(1, 1, 1, '2026-05-20 08:00:00', '2026-05-20 10:30:00', 150000, 'scheduled'),
(2, 3, 2, '2026-05-20 09:00:00', '2026-05-20 11:10:00', 180000, 'scheduled'),
(3, 4, 3, '2026-05-20 07:30:00', '2026-05-20 11:00:00', 240000, 'scheduled')
ON DUPLICATE KEY UPDATE price = VALUES(price);

INSERT INTO chatbot_questions (keyword, question, answer) VALUES
('hủy vé', 'Tôi có thể hủy vé không?', 'Bạn có thể hủy vé trong mục Lịch sử đặt vé nếu vé còn đủ điều kiện.'),
('thanh toán', 'LobiBus hỗ trợ thanh toán gì?', 'Hiện có placeholder cho tiền mặt, chuyển khoản, ví điện tử và thẻ.'),
('đổi ghế', 'Tôi muốn đổi ghế thì làm sao?', 'TODO: bổ sung nghiệp vụ đổi ghế sau khi hoàn thiện BookingService.')
ON DUPLICATE KEY UPDATE answer = VALUES(answer);
