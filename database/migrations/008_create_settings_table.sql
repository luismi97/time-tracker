-- Configuracion global de la aplicacion (fila unica).
CREATE TABLE IF NOT EXISTS settings (
    id TINYINT UNSIGNED PRIMARY KEY,
    app_name VARCHAR(100) NOT NULL DEFAULT 'Time Tracking',
    logo_path VARCHAR(255) NULL,
    attendance_mode ENUM('login', 'kiosk') NOT NULL DEFAULT 'login',
    is_24_7 TINYINT(1) NOT NULL DEFAULT 0,
    same_hours_every_day TINYINT(1) NOT NULL DEFAULT 1,
    open_time TIME NOT NULL DEFAULT '08:00:00',
    close_time TIME NOT NULL DEFAULT '17:00:00',
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (id, app_name, attendance_mode, is_24_7, same_hours_every_day, open_time, close_time)
VALUES (1, 'Time Tracking', 'login', 0, 1, '08:00:00', '17:00:00');
