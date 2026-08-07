<?php

namespace App\Services;

/**
 * Calcula totales de horas y pago para un empleado en un periodo.
 *
 * - Horas extra (>8h/dia): se pagan a 1.5x solo si employees.overtime_paid = 1;
 *   si no, se pagan como horas normales (mismo salario por hora).
 * - Hora de almuerzo: si employees.has_lunch_break = 1, se resta 1 hora de las
 *   horas regulares de cada jornada trabajada antes de calcular el pago (las
 *   horas "trabajadas" que se muestran no cambian, solo el monto a pagar).
 */
class PayrollService
{
    private const LUNCH_HOURS = 1.0;

    public function summarize(array $records, array $employee): array
    {
        $hourlyRate = (float) $employee['hourly_rate'];
        $overtimePaid = !empty($employee['overtime_paid']);
        $hasLunchBreak = !empty($employee['has_lunch_break']);

        $totalHours = 0.0;
        $totalOvertime = 0.0;
        $totalPaidHours = 0.0;
        $totalPay = 0.0;

        foreach ($records as $record) {
            $hoursWorked = (float) ($record['hours_worked'] ?? 0);
            $overtime = (float) ($record['overtime_hours'] ?? 0);
            $regular = max(0.0, $hoursWorked - $overtime);

            if ($hasLunchBreak && $hoursWorked > 0) {
                $regular = max(0.0, $regular - self::LUNCH_HOURS);
            }

            $totalHours += $hoursWorked;
            $totalOvertime += $overtime;
            $totalPaidHours += $regular + $overtime;
            $totalPay += $regular * $hourlyRate;
            $totalPay += $overtime * $hourlyRate * ($overtimePaid ? 1.5 : 1.0);
        }

        return [
            'total_hours' => round($totalHours, 2),
            'total_overtime' => round($totalOvertime, 2),
            'total_paid_hours' => round($totalPaidHours, 2),
            'total_pay' => round($totalPay, 2),
        ];
    }
}
