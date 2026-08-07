<?php

namespace App\Controllers\Employee;

use App\Core\Auth;
use App\Core\Paginator;
use App\Models\AttendanceRecord;
use App\Models\Settings;
use App\Services\AttendanceService;

class AttendanceController
{
    public function index(): void
    {
        $employee = Auth::employee();
        $page = Paginator::currentPageFromRequest();
        $perPage = 10;
        $total = AttendanceRecord::countForEmployee($employee['id']);
        $paginator = new Paginator($total, $perPage, $page);

        view('employee/attendance/index', [
            'title' => 'Mis registros',
            'records' => AttendanceRecord::paginateForEmployee($employee['id'], $perPage, $paginator->offset()),
            'paginator' => $paginator,
            'openRecord' => AttendanceRecord::openFor($employee['id']),
            'kioskMode' => Settings::get()['attendance_mode'] === 'kiosk',
        ]);
    }

    public function clockIn(): void
    {
        if (Settings::get()['attendance_mode'] === 'kiosk') {
            flash('error', 'El registro de horas se realiza desde el kiosco de asistencia.');
            redirect('/employee/attendance');
        }

        $employee = Auth::employee();
        $result = (new AttendanceService())->clockIn($employee['id']);
        flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Entrada registrada correctamente.' : $result['message']);
        redirect('/employee/attendance');
    }

    public function clockOut(): void
    {
        if (Settings::get()['attendance_mode'] === 'kiosk') {
            flash('error', 'El registro de horas se realiza desde el kiosco de asistencia.');
            redirect('/employee/attendance');
        }

        $employee = Auth::employee();
        $result = (new AttendanceService())->clockOut($employee['id']);
        flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Salida registrada correctamente.' : $result['message']);
        redirect('/employee/attendance');
    }
}
