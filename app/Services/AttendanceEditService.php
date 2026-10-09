<?php

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\AttendanceEdit;
use App\Models\AttendanceRecord;
use App\Support\IpAccess;
use DateTimeImmutable;

/**
 * Modificacion manual de una marca (por el empleado o un administrador). Toda modificacion
 * exige una justificacion y queda registrada en attendance_edits con quien la hizo.
 */
class AttendanceEditService
{
    private const MIN_REASON_LENGTH = 10;
    private const MAX_REASON_LENGTH = 500;
    private const MAX_SHIFT_HOURS = 24;

    /**
     * @param array $record Marca ya validada contra quien edita (empleado o empresa).
     * @return array<string,string> Errores por campo; vacio si se guardo.
     */
    public function update(array $record, array $input): array
    {
        $reason = trim((string) ($input['reason'] ?? ''));
        $clockIn = $this->parse($input['clock_in'] ?? '');
        $clockOutInput = trim((string) ($input['clock_out'] ?? ''));
        $clockOut = $clockOutInput === '' ? null : $this->parse($clockOutInput);
        $now = new DateTimeImmutable('now');
        $errors = [];

        if (!$clockIn) {
            $errors['clock_in'] = 'Indica una hora de entrada valida.';
        } elseif ($clockIn > $now) {
            $errors['clock_in'] = 'La entrada no puede estar en el futuro.';
        }

        if ($clockOutInput !== '' && !$clockOut) {
            $errors['clock_out'] = 'Indica una hora de salida valida.';
        } elseif (!$clockOut && $record['status'] === 'closed') {
            $errors['clock_out'] = 'Una marca cerrada debe tener hora de salida.';
        } elseif ($clockOut && $clockOut > $now) {
            $errors['clock_out'] = 'La salida no puede estar en el futuro.';
        } elseif ($clockOut && $clockIn && $clockOut <= $clockIn) {
            $errors['clock_out'] = 'La salida debe ser posterior a la entrada.';
        } elseif ($clockOut && $clockIn && ($clockOut->getTimestamp() - $clockIn->getTimestamp()) > self::MAX_SHIFT_HOURS * 3600) {
            $errors['clock_out'] = 'Una jornada no puede durar mas de ' . self::MAX_SHIFT_HOURS . ' horas.';
        }

        if (mb_strlen($reason) < self::MIN_REASON_LENGTH) {
            $errors['reason'] = 'Explica el motivo del cambio (al menos ' . self::MIN_REASON_LENGTH . ' caracteres).';
        } elseif (mb_strlen($reason) > self::MAX_REASON_LENGTH) {
            $errors['reason'] = 'La justificacion no puede superar ' . self::MAX_REASON_LENGTH . ' caracteres.';
        }

        if ($errors) {
            return $errors;
        }

        // El formulario trabaja en minutos: si el minuto no cambio se conservan los segundos originales.
        $oldIn = new DateTimeImmutable($record['clock_in']);
        $oldOut = $record['clock_out'] ? new DateTimeImmutable($record['clock_out']) : null;
        $clockIn = $this->sameMinute($clockIn, $oldIn) ? $oldIn : $clockIn;
        $clockOut = $clockOut && $oldOut && $this->sameMinute($clockOut, $oldOut) ? $oldOut : $clockOut;

        if ($clockIn == $oldIn && $clockOut == $oldOut) {
            return ['clock_in' => 'No hiciste cambios en la marca.'];
        }

        if (AttendanceRecord::overlapsOther((int) $record['employee_id'], (int) $record['id'], $clockIn, $clockOut)) {
            return ['clock_in' => 'El horario se cruza con otra marca del mismo empleado.'];
        }

        $this->save($record, $clockIn, $clockOut, $reason);
        return [];
    }

    private function save(array $record, DateTimeImmutable $clockIn, ?DateTimeImmutable $clockOut, string $reason): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            AttendanceRecord::updateTimes((int) $record['id'], $clockIn, $clockOut, (int) config('app.overtime_daily_threshold', 8));
            AttendanceEdit::create([
                'attendance_record_id' => $record['id'],
                'edited_by_user_id' => Auth::id(),
                'editor_email' => Auth::user()['email'] ?? 'desconocido',
                'editor_role' => Auth::role() ?? 'desconocido',
                'old_clock_in' => $record['clock_in'],
                'old_clock_out' => $record['clock_out'],
                'new_clock_in' => $clockIn->format('Y-m-d H:i:s'),
                'new_clock_out' => $clockOut?->format('Y-m-d H:i:s'),
                'reason' => $reason,
                'ip_address' => IpAccess::clientIp() ?: null,
            ]);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** Acepta el formato de <input type="datetime-local"> (con o sin segundos). */
    private function parse(string $value): ?DateTimeImmutable
    {
        foreach (['!Y-m-d\TH:i', '!Y-m-d\TH:i:s', '!Y-m-d H:i:s'] as $format) {
            $date = DateTimeImmutable::createFromFormat($format, trim($value));
            // La comparacion de ida y vuelta rechaza fechas desbordadas (p. ej. 31 de febrero).
            if ($date && $date->format(ltrim($format, '!')) === trim($value)) {
                return $date;
            }
        }

        return null;
    }

    private function sameMinute(DateTimeImmutable $a, DateTimeImmutable $b): bool
    {
        return $a->format('Y-m-d H:i') === $b->format('Y-m-d H:i');
    }
}
