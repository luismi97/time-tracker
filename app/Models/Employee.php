<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Tenant;

/** Todas las consultas se limitan a la empresa activa (Tenant::id()). */
class Employee
{
    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT employees.*, users.email, users.is_active AS account_active
             FROM employees JOIN users ON users.id = employees.user_id
             WHERE employees.id = ? AND employees.company_id = ?'
        );
        $stmt->execute([$id, Tenant::id()]);
        return $stmt->fetch() ?: null;
    }

    public static function findByUserId(int $userId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT employees.*, users.email
             FROM employees JOIN users ON users.id = employees.user_id
             WHERE employees.user_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    /** Usado por el kiosco: busca un empleado activo por su numero de empleado. */
    public static function findByNumber(string $employeeNumber): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT employees.*, users.email
             FROM employees JOIN users ON users.id = employees.user_id
             WHERE employees.employee_number = ? AND employees.company_id = ? AND employees.status = 'active'"
        );
        $stmt->execute([$employeeNumber, Tenant::id()]);
        return $stmt->fetch() ?: null;
    }

    /** Ultimos 5 digitos de la cedula, usados como verificacion en el kiosco (null si tiene menos de 5). */
    public static function kioskPin(?string $documentId): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $documentId);
        return strlen($digits) >= 5 ? substr($digits, -5) : null;
    }

    public static function all(): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT employees.*, users.email
             FROM employees JOIN users ON users.id = employees.user_id
             WHERE employees.company_id = ?
             ORDER BY employees.full_name'
        );
        $stmt->execute([Tenant::id()]);
        return $stmt->fetchAll();
    }

    public static function paginate(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = self::buildFilters($filters);
        $sql = "SELECT employees.*, users.email, users.is_active AS account_active
                FROM employees JOIN users ON users.id = employees.user_id
                $where ORDER BY employees.full_name ASC LIMIT $limit OFFSET $offset";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function count(array $filters): int
    {
        [$where, $params] = self::buildFilters($filters);
        $stmt = Database::connection()->prepare(
            "SELECT COUNT(*) AS total FROM employees JOIN users ON users.id = employees.user_id $where"
        );
        $stmt->execute($params);
        return (int) $stmt->fetch()['total'];
    }

    private static function buildFilters(array $filters): array
    {
        $conditions = ['employees.company_id = ?'];
        $params = [Tenant::id()];

        if (!empty($filters['search'])) {
            $conditions[] = '(employees.full_name LIKE ? OR users.email LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $conditions[] = 'employees.status = ?';
            $params[] = $filters['status'];
        }

        return ['WHERE ' . implode(' AND ', $conditions), $params];
    }

    public static function activeCount(): int
    {
        $stmt = Database::connection()->prepare("SELECT COUNT(*) AS c FROM employees WHERE company_id = ? AND status = 'active'");
        $stmt->execute([Tenant::id()]);
        return (int) $stmt->fetch()['c'];
    }

    public static function totalCount(): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM employees WHERE company_id = ?');
        $stmt->execute([Tenant::id()]);
        return (int) $stmt->fetch()['c'];
    }

    public static function create(array $data, int $userId): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO employees (company_id, user_id, employee_number, full_name, phone, address, document_id, hire_date, hourly_rate, overtime_paid, has_lunch_break, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            Tenant::id(),
            $userId,
            self::nextEmployeeNumber(),
            $data['full_name'],
            $data['phone'] ?: null,
            $data['address'] ?: null,
            $data['document_id'] ?: null,
            $data['hire_date'],
            $data['hourly_rate'],
            !empty($data['overtime_paid']) ? 1 : 0,
            !empty($data['has_lunch_break']) ? 1 : 0,
            $data['status'] ?? 'active',
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    /** Siguiente numero correlativo de empleado dentro de la empresa, con formato 001, 002, ... */
    public static function nextEmployeeNumber(): string
    {
        $stmt = Database::connection()->prepare(
            'SELECT COALESCE(MAX(CAST(employee_number AS UNSIGNED)), 0) AS m FROM employees WHERE company_id = ?'
        );
        $stmt->execute([Tenant::id()]);
        $max = (int) $stmt->fetch()['m'];

        return str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE employees SET full_name = ?, phone = ?, address = ?, document_id = ?, hire_date = ?, hourly_rate = ?, overtime_paid = ?, has_lunch_break = ?, status = ?
             WHERE id = ? AND company_id = ?'
        );
        $stmt->execute([
            $data['full_name'],
            $data['phone'] ?: null,
            $data['address'] ?: null,
            $data['document_id'] ?: null,
            $data['hire_date'],
            $data['hourly_rate'],
            !empty($data['overtime_paid']) ? 1 : 0,
            !empty($data['has_lunch_break']) ? 1 : 0,
            $data['status'] ?? 'active',
            $id,
            Tenant::id(),
        ]);
    }

    /** Elimina el usuario asociado; los registros de empleado/asistencia se eliminan en cascada. */
    public static function delete(int $id): void
    {
        $employee = self::find($id);
        if ($employee) {
            $stmt = Database::connection()->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$employee['user_id']]);
        }
    }

    public static function setStatus(int $id, string $status): void
    {
        $employee = self::find($id);
        if (!$employee) {
            return;
        }

        $stmt = Database::connection()->prepare('UPDATE employees SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        User::setActive($employee['user_id'], $status === 'active');
    }

    public static function topByHours(string $start, string $end, int $limit = 5): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT employees.id, employees.full_name, COALESCE(SUM(attendance_records.hours_worked), 0) AS total_hours
             FROM employees
             LEFT JOIN attendance_records ON attendance_records.employee_id = employees.id
                AND attendance_records.work_date BETWEEN ? AND ?
                AND attendance_records.status = 'closed'
             WHERE employees.company_id = ?
             GROUP BY employees.id, employees.full_name
             ORDER BY total_hours DESC
             LIMIT " . (int) $limit
        );
        $stmt->execute([$start, $end, Tenant::id()]);
        return $stmt->fetchAll();
    }
}
