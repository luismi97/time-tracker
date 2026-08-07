-- Datos de ejemplo (no esenciales) para explorar todas las funcionalidades del sistema:
-- multiples empleados con distinto numero de empleado, salario y fecha de ingreso, y
-- un historial de asistencia realista repartido en los ultimos dias/semanas/meses.
-- Contrasena para todos los empleados de ejemplo: Employee123!

INSERT INTO users (id, role_id, email, password_hash, is_active) VALUES
    (3, 2, 'ana.rodriguez@timetracking.test', '$2y$10$.xZoEVZZvHwTyShEFm5oK.Rj3nCcrS6JROM8a6j/TbmqE0vb3XMI2', 1),
    (4, 2, 'carlos.jimenez@timetracking.test', '$2y$10$.xZoEVZZvHwTyShEFm5oK.Rj3nCcrS6JROM8a6j/TbmqE0vb3XMI2', 1),
    (5, 2, 'laura.vargas@timetracking.test', '$2y$10$.xZoEVZZvHwTyShEFm5oK.Rj3nCcrS6JROM8a6j/TbmqE0vb3XMI2', 1),
    (6, 2, 'jose.mora@timetracking.test', '$2y$10$.xZoEVZZvHwTyShEFm5oK.Rj3nCcrS6JROM8a6j/TbmqE0vb3XMI2', 1),
    (7, 2, 'sofia.castro@timetracking.test', '$2y$10$.xZoEVZZvHwTyShEFm5oK.Rj3nCcrS6JROM8a6j/TbmqE0vb3XMI2', 0),
    (8, 2, 'diego.solano@timetracking.test', '$2y$10$.xZoEVZZvHwTyShEFm5oK.Rj3nCcrS6JROM8a6j/TbmqE0vb3XMI2', 1),
    (9, 2, 'valeria.chinchilla@timetracking.test', '$2y$10$.xZoEVZZvHwTyShEFm5oK.Rj3nCcrS6JROM8a6j/TbmqE0vb3XMI2', 0);

INSERT INTO employees (user_id, employee_number, full_name, phone, address, document_id, hire_date, hourly_rate, status) VALUES
    (3, '002', 'Ana Rodriguez', '8811-2233', 'Alajuela, Costa Rica', '2-0456-0789', '2023-03-10', 9.25, 'active'),
    (4, '003', 'Carlos Jimenez', '8822-3344', 'Cartago, Costa Rica', '3-0567-0890', '2022-11-01', 12.00, 'active'),
    (5, '004', 'Laura Vargas', '8833-4455', 'Heredia, Costa Rica', '4-0678-0901', '2024-06-15', 10.75, 'active'),
    (6, '005', 'Jose Mora', '8844-5566', 'San Jose, Costa Rica', '1-0789-0123', '2021-08-20', 14.50, 'active'),
    (7, '006', 'Sofia Castro', '8855-6677', 'Puntarenas, Costa Rica', '6-0890-1234', '2025-01-05', 7.75, 'inactive'),
    (8, '007', 'Diego Solano', '8866-7788', 'Limon, Costa Rica', '7-0901-2345', '2020-05-12', 15.00, 'active'),
    (9, '008', 'Valeria Chinchilla', '8877-8899', 'Guanacaste, Costa Rica', '5-1012-3456', '2024-09-01', 8.00, 'inactive');

-- Empleado Demo (001, employees.id = 1) -------------------------------------------
INSERT INTO attendance_records (employee_id, work_date, clock_in, clock_out, hours_worked, overtime_hours, status) VALUES
    (1, CURDATE(), CONCAT(CURDATE(), ' 08:00:00'), CONCAT(CURDATE(), ' 12:00:00'), 4.00, 0.00, 'closed'),
    (1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 18:00:00'), 10.00, 2.00, 'closed'),
    (1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 15:00:00'), 7.00, 0.00, 'closed'),
    (1, DATE_SUB(CURDATE(), INTERVAL 8 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 16:00:00'), 8.00, 0.00, 'closed');

-- Ana Rodriguez (002, employees.id = 2) --------------------------------------------
INSERT INTO attendance_records (employee_id, work_date, clock_in, clock_out, hours_worked, overtime_hours, status) VALUES
    (2, CURDATE(), CONCAT(CURDATE(), ' 07:30:00'), CONCAT(CURDATE(), ' 11:30:00'), 4.00, 0.00, 'closed'),
    (2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 07:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 16:00:00'), 8.50, 0.50, 'closed'),
    (2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 07:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 15:30:00'), 8.00, 0.00, 'closed'),
    (2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 07:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 15:30:00'), 8.00, 0.00, 'closed'),
    (2, DATE_SUB(CURDATE(), INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 07:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 15:30:00'), 8.00, 0.00, 'closed'),
    (2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 07:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 15:30:00'), 8.00, 0.00, 'closed'),
    (2, DATE_SUB(CURDATE(), INTERVAL 9 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 9 DAY), ' 07:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 9 DAY), ' 17:30:00'), 10.00, 2.00, 'closed');

-- Carlos Jimenez (003, employees.id = 3) -------------------------------------------
INSERT INTO attendance_records (employee_id, work_date, clock_in, clock_out, hours_worked, overtime_hours, status) VALUES
    (3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 17:00:00'), 8.00, 0.00, 'closed'),
    (3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 19:00:00'), 10.00, 2.00, 'closed'),
    (3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 17:00:00'), 8.00, 0.00, 'closed'),
    (3, DATE_SUB(CURDATE(), INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 17:00:00'), 8.00, 0.00, 'closed'),
    (3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 10 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 10 DAY), ' 17:00:00'), 8.00, 0.00, 'closed'),
    (3, DATE_SUB(CURDATE(), INTERVAL 35 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 35 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 35 DAY), ' 17:00:00'), 8.00, 0.00, 'closed');

-- Laura Vargas (004, employees.id = 4) ---------------------------------------------
INSERT INTO attendance_records (employee_id, work_date, clock_in, clock_out, hours_worked, overtime_hours, status) VALUES
    (4, CURDATE(), CONCAT(CURDATE(), ' 08:00:00'), CONCAT(CURDATE(), ' 12:30:00'), 4.50, 0.00, 'closed'),
    (4, DATE_SUB(CURDATE(), INTERVAL 2 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (4, DATE_SUB(CURDATE(), INTERVAL 4 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (4, DATE_SUB(CURDATE(), INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (4, DATE_SUB(CURDATE(), INTERVAL 9 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 9 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 9 DAY), ' 16:00:00'), 8.00, 0.00, 'closed');

-- Jose Mora (005, employees.id = 5) ------------------------------------------------
INSERT INTO attendance_records (employee_id, work_date, clock_in, clock_out, hours_worked, overtime_hours, status) VALUES
    (5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 06:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 14:00:00'), 8.00, 0.00, 'closed'),
    (5, DATE_SUB(CURDATE(), INTERVAL 3 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 06:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 16:00:00'), 10.00, 2.00, 'closed'),
    (5, DATE_SUB(CURDATE(), INTERVAL 5 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 06:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 14:00:00'), 8.00, 0.00, 'closed'),
    (5, DATE_SUB(CURDATE(), INTERVAL 8 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 06:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 14:00:00'), 8.00, 0.00, 'closed'),
    (5, DATE_SUB(CURDATE(), INTERVAL 11 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 06:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 11 DAY), ' 14:00:00'), 8.00, 0.00, 'closed'),
    (5, DATE_SUB(CURDATE(), INTERVAL 200 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 200 DAY), ' 06:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 200 DAY), ' 14:00:00'), 8.00, 0.00, 'closed');

-- Diego Solano (007, employees.id = 7) ---------------------------------------------
INSERT INTO attendance_records (employee_id, work_date, clock_in, clock_out, hours_worked, overtime_hours, status) VALUES
    (7, CURDATE(), CONCAT(CURDATE(), ' 08:00:00'), CONCAT(CURDATE(), ' 12:00:00'), 4.00, 0.00, 'closed'),
    (7, DATE_SUB(CURDATE(), INTERVAL 1 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (7, DATE_SUB(CURDATE(), INTERVAL 2 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (7, DATE_SUB(CURDATE(), INTERVAL 5 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 19:00:00'), 11.00, 3.00, 'closed'),
    (7, DATE_SUB(CURDATE(), INTERVAL 9 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 9 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 9 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (7, DATE_SUB(CURDATE(), INTERVAL 13 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 13 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 13 DAY), ' 16:00:00'), 8.00, 0.00, 'closed');

-- Historial de los empleados inactivos (registrado antes de desactivarlos) --------
INSERT INTO attendance_records (employee_id, work_date, clock_in, clock_out, hours_worked, overtime_hours, status) VALUES
    (6, DATE_SUB(CURDATE(), INTERVAL 60 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 60 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 60 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (6, DATE_SUB(CURDATE(), INTERVAL 61 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 61 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 61 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (8, DATE_SUB(CURDATE(), INTERVAL 45 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 45 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 45 DAY), ' 16:00:00'), 8.00, 0.00, 'closed'),
    (8, DATE_SUB(CURDATE(), INTERVAL 46 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 46 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 46 DAY), ' 16:00:00'), 8.00, 0.00, 'closed');
