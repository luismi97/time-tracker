-- Reglas de pago configurables por empleado (por defecto: horas extra NO se pagan con recargo).
ALTER TABLE employees
    ADD COLUMN overtime_paid TINYINT(1) NOT NULL DEFAULT 0 AFTER hourly_rate,
    ADD COLUMN has_lunch_break TINYINT(1) NOT NULL DEFAULT 0 AFTER overtime_paid;
