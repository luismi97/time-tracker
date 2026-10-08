-- Red autorizada por empresa: IPs o rangos CIDR (uno por linea) desde donde los empleados
-- pueden iniciar sesion y usar el kiosco. NULL = sin restriccion. No aplica a administradores.
ALTER TABLE settings
    ADD COLUMN allowed_ips TEXT NULL AFTER attendance_mode;
