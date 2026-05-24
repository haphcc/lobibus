USE lobibus;

DELIMITER //

DROP PROCEDURE IF EXISTS booking_module_compat//

CREATE PROCEDURE booking_module_compat()
BEGIN
    ALTER TABLE bookings
    MODIFY status ENUM('pending','confirmed','cancelled','completed','expired') NOT NULL DEFAULT 'pending';

    ALTER TABLE payments
    MODIFY status ENUM('pending','paid','failed','refunded','cancelled') NOT NULL DEFAULT 'pending';

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'trips'
          AND COLUMN_NAME = 'available_seats'
    ) THEN
        ALTER TABLE trips
        ADD COLUMN available_seats INT NULL AFTER price;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'booking_details'
          AND COLUMN_NAME = 'trip_id'
    ) THEN
        ALTER TABLE booking_details
        ADD COLUMN trip_id INT NULL AFTER booking_id;
    END IF;

    UPDATE booking_details bd
    JOIN bookings b ON b.id = bd.booking_id
    SET bd.trip_id = b.trip_id
    WHERE bd.trip_id IS NULL;

    ALTER TABLE booking_details
    MODIFY trip_id INT NOT NULL;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'booking_details'
          AND CONSTRAINT_NAME = 'fk_booking_details_trip'
    ) THEN
        ALTER TABLE booking_details
        ADD CONSTRAINT fk_booking_details_trip
        FOREIGN KEY (trip_id) REFERENCES trips(id);
    END IF;

    IF EXISTS (
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'booking_details'
          AND INDEX_NAME = 'uq_trip_seat'
    ) THEN
        ALTER TABLE booking_details
        DROP INDEX uq_trip_seat;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'booking_details'
          AND INDEX_NAME = 'idx_booking_details_trip_seat'
    ) THEN
        CREATE INDEX idx_booking_details_trip_seat
        ON booking_details(trip_id, seat_id);
    END IF;

    UPDATE trips t
    JOIN buses bus ON bus.id = t.bus_id
    LEFT JOIN (
        SELECT bd.trip_id, COUNT(DISTINCT bd.seat_id) AS booked_count
        FROM booking_details bd
        JOIN bookings b ON b.id = bd.booking_id
        WHERE b.status NOT IN ('cancelled', 'expired')
        GROUP BY bd.trip_id
    ) booked ON booked.trip_id = t.id
    SET t.available_seats = GREATEST(bus.total_seats - COALESCE(booked.booked_count, 0), 0);

    ALTER TABLE trips
    MODIFY available_seats INT NOT NULL;
END//

DELIMITER ;

CALL booking_module_compat();
DROP PROCEDURE booking_module_compat;
