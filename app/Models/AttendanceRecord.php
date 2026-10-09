<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Tenant;
use DateTimeImmutable;

/**
 * Los metodos por empleado reciben ids ya validados contra la empresa activa;
 * los listados y totales globales filtran por Tenant::id() via employees.company_id.
 */
class AttendanceRecord
{
    public static function openFor(int $employeeId): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM attendance_records WHERE employee_id = ? AND status = 'open' LIMIT 1 FOR UPDATE"
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetch() ?: null;
    }

    public static function clockIn(int $employeeId): array
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            if (self::openFor($employeeId)) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Ya existe una jornada abierta. Debes registrar la salida primero.'];
            }

            $now = new DateTimeImmutable('now');
            $stmt = $pdo->prepare(
                "INSERT INTO attendance_records (employee_id, work_date, clock_in, status) VALUES (?, ?, ?, 'open')"
            );
            $stmt->execute([$employeeId, $now->format('Y-m-d'), $now->format('Y-m-d H:i:s')]);
            $pdo->commit();
            return ['success' => true, 'clock_in' => $now->format('Y-m-d H:i:s')];
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function clockOut(int $employeeId, int $overtimeThreshold): array
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $open = self::openFor($employeeId);
            if (!$open) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'No tienes una entrada registrada para marcar la salida.'];
            }

            $clockIn = new DateTimeImmutable($open['clock_in']);
            $now = new DateTimeImmutable('now');

            if ($now <= $clockIn) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'La hora de salida debe ser posterior a la entrada.'];
            }

            [$hours, $overtime] = self::computeHours($clockIn, $now, $overtimeThreshold);

            $stmt = $pdo->prepare(
                "UPDATE attendance_records SET clock_out = ?, hours_worked = ?, overtime_hours = ?, status = 'closed' WHERE id = ?"
            );
            $stmt->execute([$now->format('Y-m-d H:i:s'), $hours, $overtime, $open['id']]);
            $pdo->commit();
            return [
                'success' => true,
                'clock_in' => $open['clock_in'],
                'clock_out' => $now->format('Y-m-d H:i:s'),
                'hours_worked' => $hours,
            ];
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** Registros de un empleado ordenados cronologicamente (usado para reportes). */
    public static function forEmployeeInRange(int $employeeId, string $start, string $end): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT attendance_records.*, (SELECT COUNT(*) FROM attendance_edits WHERE attendance_edits.attendance_record_id = attendance_records.id) AS edit_count
             FROM attendance_records WHERE employee_id = ? AND work_date BETWEEN ? AND ?
             ORDER BY work_date ASC, clock_in ASC'
        );
        $stmt->execute([$employeeId, $start, $end]);
        return $stmt->fetchAll();
    }

    public static function paginateForEmployee(int $employeeId, int $limit, int $offset): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT attendance_records.*, (SELECT COUNT(*) FROM attendance_edits WHERE attendance_edits.attendance_record_id = attendance_records.id) AS edit_count
             FROM attendance_records WHERE employee_id = ?
             ORDER BY work_date DESC, clock_in DESC LIMIT $limit OFFSET $offset"
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }

    public static function countForEmployee(int $employeeId): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM attendance_records WHERE employee_id = ?');
        $stmt->execute([$employeeId]);
        return (int) $stmt->fetch()['c'];
    }

    public static function sumHours(int $employeeId, string $start, string $end): float
    {
        $stmt = Database::connection()->prepare(
            "SELECT COALESCE(SUM(hours_worked), 0) AS total FROM attendance_records
             WHERE employee_id = ? AND work_date BETWEEN ? AND ? AND status = 'closed'"
        );
        $stmt->execute([$employeeId, $start, $end]);
        return (float) $stmt->fetch()['total'];
    }

    public static function sumHoursGlobal(string $start, string $end): float
    {
        $stmt = Database::connection()->prepare(
            "SELECT COALESCE(SUM(attendance_records.hours_worked), 0) AS total
             FROM attendance_records JOIN employees ON employees.id = attendance_records.employee_id
             WHERE employees.company_id = ? AND attendance_records.work_date BETWEEN ? AND ?
                AND attendance_records.status = 'closed'"
        );
        $stmt->execute([Tenant::id(), $start, $end]);
        return (float) $stmt->fetch()['total'];
    }

    public static function paginateAll(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = self::buildFilters($filters);
        $sql = "SELECT attendance_records.*, employees.full_name, (SELECT COUNT(*) FROM attendance_edits WHERE attendance_edits.attendance_record_id = attendance_records.id) AS edit_count
                FROM attendance_records JOIN employees ON employees.id = attendance_records.employee_id
                $where ORDER BY attendance_records.work_date DESC, attendance_records.clock_in DESC
                LIMIT $limit OFFSET $offset";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countAll(array $filters): int
    {
        [$where, $params] = self::buildFilters($filters);
        $stmt = Database::connection()->prepare(
            "SELECT COUNT(*) AS c FROM attendance_records JOIN employees ON employees.id = attendance_records.employee_id $where"
        );
        $stmt->execute($params);
        return (int) $stmt->fetch()['c'];
    }

    private static function buildFilters(array $filters): array
    {
        $conditions = ['employees.company_id = ?'];
        $params = [Tenant::id()];

        if (!empty($filters['employee_id'])) {
            $conditions[] = 'attendance_records.employee_id = ?';
            $params[] = $filters['employee_id'];
        }

        if (!empty($filters['start'])) {
            $conditions[] = 'attendance_records.work_date >= ?';
            $params[] = $filters['start'];
        }

        if (!empty($filters['end'])) {
            $conditions[] = 'attendance_records.work_date <= ?';
            $params[] = $filters['end'];
        }

        return ['WHERE ' . implode(' AND ', $conditions), $params];
    }

    public static function recent(int $limit = 8): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT attendance_records.*, employees.full_name
             FROM attendance_records JOIN employees ON employees.id = attendance_records.employee_id
             WHERE employees.company_id = ?
             ORDER BY attendance_records.created_at DESC LIMIT ' . (int) $limit
        );
        $stmt->execute([Tenant::id()]);
        return $stmt->fetchAll();
    }

    /** Marca de la empresa activa, con datos del empleado (edicion por administradores). */
    public static function findForCompany(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT attendance_records.*, employees.full_name, employees.employee_number
             FROM attendance_records JOIN employees ON employees.id = attendance_records.employee_id
             WHERE attendance_records.id = ? AND employees.company_id = ?'
        );
        $stmt->execute([$id, Tenant::id()]);
        return $stmt->fetch() ?: null;
    }

    /** Marca de un empleado especifico (edicion por el propio empleado). */
    public static function findForEmployee(int $id, int $employeeId): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM attendance_records WHERE id = ? AND employee_id = ?');
        $stmt->execute([$id, $employeeId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * true si el rango [entrada, salida] choca con otra marca del empleado. Las marcas
     * abiertas (y un rango sin salida) se consideran abiertas hasta ahora.
     */
    public static function overlapsOther(int $employeeId, int $excludeId, DateTimeImmutable $clockIn, ?DateTimeImmutable $clockOut): bool
    {
        $end = ($clockOut ?? new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM attendance_records
             WHERE employee_id = ? AND id != ? AND clock_in < ? AND COALESCE(clock_out, NOW()) > ?'
        );
        $stmt->execute([$employeeId, $excludeId, $end, $clockIn->format('Y-m-d H:i:s')]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Guarda nuevas horas de una marca y recalcula horas trabajadas, extra, fecha y estado. */
    public static function updateTimes(int $id, DateTimeImmutable $clockIn, ?DateTimeImmutable $clockOut, int $overtimeThreshold): void
    {
        [$hours, $overtime] = $clockOut ? self::computeHours($clockIn, $clockOut, $overtimeThreshold) : [null, 0];

        $stmt = Database::connection()->prepare(
            'UPDATE attendance_records
             SET work_date = ?, clock_in = ?, clock_out = ?, hours_worked = ?, overtime_hours = ?, status = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $clockIn->format('Y-m-d'),
            $clockIn->format('Y-m-d H:i:s'),
            $clockOut?->format('Y-m-d H:i:s'),
            $hours,
            $overtime,
            $clockOut ? 'closed' : 'open',
            $id,
        ]);
    }

    /** @return array{0: float, 1: float} Horas trabajadas y horas extra (sobre el umbral diario). */
    private static function computeHours(DateTimeImmutable $clockIn, DateTimeImmutable $clockOut, int $overtimeThreshold): array
    {
        $hours = round(($clockOut->getTimestamp() - $clockIn->getTimestamp()) / 3600, 2);
        return [$hours, round(max(0, $hours - $overtimeThreshold), 2)];
    }

    public static function todayFor(int $employeeId): ?array
    {
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        $stmt = Database::connection()->prepare(
            'SELECT * FROM attendance_records WHERE employee_id = ? AND work_date = ? ORDER BY clock_in DESC LIMIT 1'
        );
        $stmt->execute([$employeeId, $today]);
        return $stmt->fetch() ?: null;
    }
}
