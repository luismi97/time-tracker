<?php

namespace App\Models;

use App\Core\Database;

/** Bitacora de modificaciones manuales de marcas. */
class AttendanceEdit
{
    public static function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO attendance_edits
                (attendance_record_id, edited_by_user_id, editor_email, editor_role,
                 old_clock_in, old_clock_out, new_clock_in, new_clock_out, reason, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['attendance_record_id'],
            $data['edited_by_user_id'],
            $data['editor_email'],
            $data['editor_role'],
            $data['old_clock_in'],
            $data['old_clock_out'],
            $data['new_clock_in'],
            $data['new_clock_out'],
            $data['reason'],
            $data['ip_address'],
        ]);
    }

    /** Historial de una marca, del cambio mas reciente al mas antiguo. */
    public static function forRecord(int $recordId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM attendance_edits WHERE attendance_record_id = ? ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute([$recordId]);
        return $stmt->fetchAll();
    }
}
