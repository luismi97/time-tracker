-- Horario manual por dia, usado cuando settings.same_hours_every_day = 0 y is_24_7 = 0.
CREATE TABLE IF NOT EXISTS business_hours (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    day_of_week TINYINT UNSIGNED NOT NULL UNIQUE COMMENT '0=Domingo ... 6=Sabado',
    is_open TINYINT(1) NOT NULL DEFAULT 1,
    open_time TIME NULL,
    close_time TIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO business_hours (day_of_week, is_open, open_time, close_time) VALUES
    (0, 0, NULL, NULL),
    (1, 1, '08:00:00', '17:00:00'),
    (2, 1, '08:00:00', '17:00:00'),
    (3, 1, '08:00:00', '17:00:00'),
    (4, 1, '08:00:00', '17:00:00'),
    (5, 1, '08:00:00', '17:00:00'),
    (6, 0, NULL, NULL);
