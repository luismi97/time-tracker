<?php

namespace App\Controllers\Admin;

use App\Core\Paginator;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Support\DateRange;

class AttendanceController
{
    public function index(): void
    {
        $period = $_GET['period'] ?? '';
        $queryFilters = ['employee_id' => $_GET['employee_id'] ?? ''];

        if ($period !== '') {
            [$start, $end] = DateRange::resolve($period, $_GET);
            $queryFilters['start'] = $start->format('Y-m-d');
            $queryFilters['end'] = $end->format('Y-m-d');
        }

        $page = Paginator::currentPageFromRequest();
        $perPage = 15;
        $total = AttendanceRecord::countAll($queryFilters);
        $paginator = new Paginator($total, $perPage, $page);

        view('admin/attendance/index', [
            'title' => 'Registros de asistencia',
            'records' => AttendanceRecord::paginateAll($queryFilters, $perPage, $paginator->offset()),
            'paginator' => $paginator,
            'employees' => Employee::all(),
            'filters' => array_merge(['period' => $period], $_GET),
        ]);
    }
}
