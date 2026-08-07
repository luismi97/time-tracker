<?php

namespace App\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Settings;
use App\Services\AttendanceService;

/** Kiosco publico: marcar entrada/salida usando el numero de empleado (sin iniciar sesion). */
class KioskController
{
    public function show(): void
    {
        $this->ensureEnabled();
        view('kiosk/index', ['title' => 'Marcar asistencia', 'layout' => 'layouts/guest']);
    }

    public function lookup(): void
    {
        $this->ensureEnabled();
        $employee = $this->findEmployeeOrFail();
        $this->renderStatus($employee);
    }

    public function clockIn(): void
    {
        $this->ensureEnabled();
        $employee = $this->findEmployeeOrFail();

        $result = (new AttendanceService())->clockIn($employee['id']);
        if (!$result['success']) {
            flash('error', $result['message']);
        }

        $this->renderStatus($employee, $result['success'] ? 'in' : null);
    }

    public function clockOut(): void
    {
        $this->ensureEnabled();
        $employee = $this->findEmployeeOrFail();

        $result = (new AttendanceService())->clockOut($employee['id']);
        if (!$result['success']) {
            flash('error', $result['message']);
        }

        $this->renderStatus($employee, $result['success'] ? 'out' : null);
    }

    private function findEmployeeOrFail(): array
    {
        $number = trim($_POST['employee_number'] ?? '');
        $employee = $number !== '' ? Employee::findByNumber($number) : null;

        if (!$employee) {
            flash('error', 'Codigo de empleado invalido o inactivo.');
            redirect('/kiosk');
        }

        return $employee;
    }

    private function renderStatus(array $employee, ?string $justAction = null): void
    {
        view('kiosk/index', [
            'title' => 'Marcar asistencia',
            'layout' => 'layouts/guest',
            'employee' => $employee,
            'todayRecord' => AttendanceRecord::todayFor($employee['id']),
            'openRecord' => AttendanceRecord::openFor($employee['id']),
            'justAction' => $justAction,
        ]);
    }

    private function ensureEnabled(): void
    {
        if (Settings::get()['attendance_mode'] !== 'kiosk') {
            flash('error', 'El registro con codigo de empleado no esta habilitado.');
            redirect('/login');
        }
    }
}
