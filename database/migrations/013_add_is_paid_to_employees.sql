-- Empleados sin salario (practicantes, voluntarios): registran horas pero no generan pago.
ALTER TABLE employees
    ADD COLUMN is_paid TINYINT(1) NOT NULL DEFAULT 1 AFTER hire_date;
