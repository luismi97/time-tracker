-- Bitacora de modificaciones manuales de marcas (por empleados o administradores).
-- Guarda quien, cuando, desde que IP, los valores anteriores/nuevos y la justificacion.
-- editor_email/editor_role son una copia: el registro se conserva aunque se elimine el usuario.
CREATE TABLE IF NOT EXISTS attendance_edits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attendance_record_id INT UNSIGNED NOT NULL,
    edited_by_user_id INT UNSIGNED NULL,
    editor_email VARCHAR(150) NOT NULL,
    editor_role VARCHAR(30) NOT NULL,
    old_clock_in DATETIME NOT NULL,
    old_clock_out DATETIME NULL,
    new_clock_in DATETIME NOT NULL,
    new_clock_out DATETIME NULL,
    reason TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attendance_edits_record FOREIGN KEY (attendance_record_id) REFERENCES attendance_records (id) ON DELETE CASCADE,
    CONSTRAINT fk_attendance_edits_user FOREIGN KEY (edited_by_user_id) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_attendance_edits_record (attendance_record_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
