USE lobibus;

DELIMITER //

DROP PROCEDURE IF EXISTS roundtrip_booking_group//

CREATE PROCEDURE roundtrip_booking_group()
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'bookings'
          AND COLUMN_NAME = 'booking_group_code'
    ) THEN
        ALTER TABLE bookings
        ADD COLUMN booking_group_code VARCHAR(40) NULL AFTER booking_code;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'bookings'
          AND COLUMN_NAME = 'trip_type'
    ) THEN
        ALTER TABLE bookings
        ADD COLUMN trip_type ENUM('oneway','roundtrip') NOT NULL DEFAULT 'oneway' AFTER booking_group_code;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'bookings'
          AND COLUMN_NAME = 'direction'
    ) THEN
        ALTER TABLE bookings
        ADD COLUMN direction ENUM('outbound','return') NOT NULL DEFAULT 'outbound' AFTER trip_type;
    END IF;

    UPDATE bookings
    SET trip_type = 'oneway',
        direction = 'outbound'
    WHERE trip_type IS NULL OR direction IS NULL;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'bookings'
          AND INDEX_NAME = 'idx_bookings_group'
    ) THEN
        CREATE INDEX idx_bookings_group
        ON bookings(booking_group_code);
    END IF;
END//

DELIMITER ;

CALL roundtrip_booking_group();
DROP PROCEDURE roundtrip_booking_group;
