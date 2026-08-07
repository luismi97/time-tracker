<?php

namespace App\Controllers\Admin;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\User;
use App\Support\DateRange;
use DateTimeImmutable;

class DashboardController
{
    public function index(): void
    {
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        [$weekStart, $weekEnd] = DateRange::resolve('week', []);
        [$monthStart, $monthEnd] = DateRange::resolve('month', []);

        $stats = [
            'total_employees' => Employee::totalCount(),
            'active_employees' => Employee::activeCount(),
            'hours_today' => AttendanceRecord::sumHoursGlobal($today, $today),
            'hours_week' => AttendanceRecord::sumHoursGlobal($weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')),
            'hours_month' => AttendanceRecord::sumHoursGlobal($monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')),
        ];

        view('admin/dashboard', [
            'title' => 'Panel de administracion',
            'stats' => $stats,
            'recentRecords' => AttendanceRecord::recent(8),
            'topEmployees' => Employee::topByHours($monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d'), 5),
            'recentLogins' => User::recentLogins(5),
        ]);
    }
}
