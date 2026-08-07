<?php

namespace App\Controllers\Employee;

use App\Core\Auth;
use App\Models\AttendanceRecord;
use App\Support\DateRange;
use DateTimeImmutable;

class DashboardController
{
    public function index(): void
    {
        $employee = Auth::employee();
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        [$weekStart, $weekEnd] = DateRange::resolve('week', []);

        view('employee/dashboard', [
            'title' => 'Mi panel',
            'employee' => $employee,
            'todayRecord' => AttendanceRecord::todayFor($employee['id']),
            'hoursToday' => AttendanceRecord::sumHours($employee['id'], $today, $today),
            'hoursWeek' => AttendanceRecord::sumHours($employee['id'], $weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')),
        ]);
    }
}
