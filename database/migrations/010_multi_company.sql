-- Multiempresa: cada empresa es independiente (empleados, asistencia, configuracion y horario propios).
-- Los datos existentes pasan a la empresa 1 y se agrega un super administrador que gestiona las empresas.

CREATE TABLE IF NOT EXISTS companies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE COMMENT 'Codigo publico de la empresa (usado en la URL del kiosco)',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO companies (id, name, slug)
VALUES (1, COALESCE((SELECT app_name FROM settings WHERE id = 1), 'Empresa principal'), 'principal');

INSERT INTO roles (id, name) VALUES (3, 'super_admin');

-- Usuarios: company_id es NULL solo para el super admin.
ALTER TABLE users
    ADD COLUMN company_id INT UNSIGNED NULL AFTER role_id,
    ADD CONSTRAINT fk_users_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE;

UPDATE users SET company_id = 1;

-- Contrasena super admin: SuperAdmin123!
INSERT INTO users (role_id, company_id, email, password_hash, is_active)
VALUES (3, NULL, 'superadmin@timetracking.test', '$2y$10$PGMWFIKTczPp0wE2Xr4F4uHyxXLuQou7ZvVpDNhYfMITBVc4PdBJq', 1);

-- Empleados: el numero de empleado es unico dentro de cada empresa.
ALTER TABLE employees ADD COLUMN company_id INT UNSIGNED NULL AFTER id;
UPDATE employees SET company_id = 1;
ALTER TABLE employees
    MODIFY company_id INT UNSIGNED NOT NULL,
    DROP INDEX employee_number,
    ADD UNIQUE KEY uq_employees_company_number (company_id, employee_number),
    ADD CONSTRAINT fk_employees_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE;

-- Configuracion: una fila por empresa. El nombre del sitio pasa a ser companies.name.
ALTER TABLE settings
    MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN company_id INT UNSIGNED NULL AFTER id;
UPDATE settings SET company_id = 1;
ALTER TABLE settings
    MODIFY company_id INT UNSIGNED NOT NULL,
    DROP COLUMN app_name,
    ADD UNIQUE KEY uq_settings_company (company_id),
    ADD CONSTRAINT fk_settings_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE;

-- Horario manual: 7 filas por empresa.
ALTER TABLE business_hours
    MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ADD COLUMN company_id INT UNSIGNED NULL AFTER id;
UPDATE business_hours SET company_id = 1;
ALTER TABLE business_hours
    MODIFY company_id INT UNSIGNED NOT NULL,
    DROP INDEX day_of_week,
    ADD UNIQUE KEY uq_business_hours_company_day (company_id, day_of_week),
    ADD CONSTRAINT fk_business_hours_company FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE;
