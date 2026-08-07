-- Datos iniciales para poder usar la aplicacion inmediatamente despues de levantarla.
INSERT INTO roles (id, name) VALUES (1, 'admin'), (2, 'employee');

-- Contrasena admin: Admin123!  |  Contrasena empleado demo: Employee123!
INSERT INTO users (id, role_id, email, password_hash, is_active) VALUES
    (1, 1, 'admin@timetracking.test', '$2y$10$JVtd6qEMPOBLmU7iAeOWye1qMtSuxYWody.zrO..KcXE3aeCFmO8O', 1),
    (2, 2, 'empleado@timetracking.test', '$2y$10$.xZoEVZZvHwTyShEFm5oK.Rj3nCcrS6JROM8a6j/TbmqE0vb3XMI2', 1);

INSERT INTO employees (user_id, employee_number, full_name, phone, address, document_id, hire_date, hourly_rate, status) VALUES
    (2, '001', 'Empleado Demo', '8888-0000', 'San Jose, Costa Rica', '1-2345-6789', CURDATE(), 8.50, 'active');
