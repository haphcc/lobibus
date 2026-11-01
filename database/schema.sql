CREATE DATABASE IF NOT EXISTS lobibus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lobibus;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    status ENUM('active','locked') NOT NULL DEFAULT 'active',
    is_google TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    province VARCHAR(120),
    address VARCHAR(255),
    latitude DECIMAL(10, 7),
    longitude DECIMAL(10, 7),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_location_id INT NOT NULL,
    to_location_id INT NOT NULL,
    distance_km DECIMAL(8, 2),
    duration_minutes INT,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_routes_from FOREIGN KEY (from_location_id) REFERENCES locations(id),
    CONSTRAINT fk_routes_to FOREIGN KEY (to_location_id) REFERENCES locations(id)
);

CREATE TABLE buses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    license_plate VARCHAR(30) NOT NULL UNIQUE,
    bus_type ENUM('standard','sleeper','limousine') NOT NULL DEFAULT 'standard',
    total_seats INT NOT NULL,
    image VARCHAR(255),
    status ENUM('active','maintenance','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    seat_type ENUM('standard','sleeper','vip') NOT NULL DEFAULT 'standard',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_bus_seat (bus_id, seat_number),
    CONSTRAINT fk_seats_bus FOREIGN KEY (bus_id) REFERENCES buses(id)
);

CREATE TABLE trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT NOT NULL,
    bus_id INT NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    status ENUM('scheduled','running','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_trips_route FOREIGN KEY (route_id) REFERENCES routes(id),
    CONSTRAINT fk_trips_bus FOREIGN KEY (bus_id) REFERENCES buses(id)
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    trip_id INT NOT NULL,
    booking_code VARCHAR(30) NOT NULL UNIQUE,
    customer_name VARCHAR(120) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(150),
    total_amount DECIMAL(12, 2) NOT NULL,

    -- thêm expired vào status để tự động hủy booking nếu khách không thanh toán quá lâu
    status ENUM('pending','confirmed','cancelled','completed','expired') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_bookings_trip FOREIGN KEY (trip_id) REFERENCES trips(id)
);

CREATE TABLE booking_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    seat_id INT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_booking_seat (booking_id, seat_id),
    CONSTRAINT fk_booking_details_booking FOREIGN KEY (booking_id) REFERENCES bookings(id),
    CONSTRAINT fk_booking_details_seat FOREIGN KEY (seat_id) REFERENCES seats(id)
);

CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    ticket_code VARCHAR(40) NOT NULL UNIQUE,
    qr_code_path VARCHAR(255),
    status ENUM('valid','used','cancelled') NOT NULL DEFAULT 'valid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tickets_booking FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    method ENUM('cash','bank_transfer','momo','zalopay','card') NOT NULL DEFAULT 'cash',
    amount DECIMAL(12, 2) NOT NULL,
    -- thêm cancelled nếu người dùng hủy thanh toán trước khi trả tiền
    status ENUM('pending','paid','failed','refunded','cancelled') NOT NULL DEFAULT 'pending',
    transaction_code VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    trip_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_reviews_trip FOREIGN KEY (trip_id) REFERENCES trips(id)
);

CREATE TABLE chatbot_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(120) NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- thêm trip_id vào booking_details để đảm bảo mỗi chỗ chỉ được đặt một lần cho mỗi chuyến
ALTER TABLE booking_details
ADD COLUMN trip_id INT NOT NULL AFTER booking_id;

ALTER TABLE booking_details
ADD CONSTRAINT fk_booking_details_trip
FOREIGN KEY (trip_id) REFERENCES trips(id);

ALTER TABLE booking_details
ADD UNIQUE KEY uq_trip_seat (trip_id, seat_id);

-- thêm check
ALTER TABLE reviews
ADD CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5);

ALTER TABLE trips
ADD CONSTRAINT chk_trips_time CHECK (arrival_time > departure_time);

ALTER TABLE trips
ADD CONSTRAINT chk_trips_price CHECK (price >= 0);

ALTER TABLE buses
ADD CONSTRAINT chk_buses_total_seats CHECK (total_seats > 0);

ALTER TABLE booking_details
ADD CONSTRAINT chk_booking_details_price CHECK (price >= 0);

ALTER TABLE payments
ADD CONSTRAINT chk_payments_amount CHECK (amount >= 0);

-- thêm index
-- tìm chuyến xe
CREATE INDEX idx_routes_from_to_status
ON routes(from_location_id, to_location_id, status);

CREATE INDEX idx_trips_route_departure_status
ON trips(route_id, departure_time, status);

CREATE INDEX idx_trips_bus_departure
ON trips(bus_id, departure_time);

-- tìm booking
CREATE INDEX idx_tickets_booking
ON tickets(booking_id);

CREATE INDEX idx_payments_booking_status
ON payments(booking_id, status);

CREATE INDEX idx_payments_transaction_code
ON payments(transaction_code);

-- chatbot
CREATE INDEX idx_chatbot_keyword
ON chatbot_questions(keyword);
