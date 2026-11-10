USE lobibus;

ALTER TABLE bookings
MODIFY status ENUM('pending','confirmed','cancelled','completed','expired') NOT NULL DEFAULT 'pending';
