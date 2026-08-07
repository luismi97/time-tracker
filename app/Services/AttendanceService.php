<?php

namespace App\Services;

use App\Models\AttendanceRecord;

class AttendanceService
{
    public function clockIn(int $employeeId): array
    {
        return AttendanceRecord::clockIn($employeeId);
    }

    public function clockOut(int $employeeId): array
    {
        $threshold = (int) config('app.overtime_daily_threshold', 8);
        return AttendanceRecord::clockOut($employeeId, $threshold);
    }
}
