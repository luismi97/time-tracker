<?php

namespace App\Controllers\Employee;

use App\Core\Auth;
use App\Core\Paginator;
use App\Core\Tenant;
use App\Models\AttendanceEdit;
use App\Models\AttendanceRecord;
use App\Models\Settings;
use App\Services\AttendanceEditService;
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
            'kioskUrl' => '/kiosk/' . rawurlencode(Tenant::company()['slug']),
        ]);
    }

    /** El empleado corrige una de sus marcas; la justificacion es obligatoria y queda registrada. */
    public function edit(string $id): void
    {
        $record = $this->findOwnRecordOrFail($id);

        view('employee/attendance/edit', [
            'title' => 'Modificar marca',
            'record' => $record,
            'edits' => AttendanceEdit::forRecord((int) $record['id']),
        ]);
    }

    public function update(string $id): void
    {
        $record = $this->findOwnRecordOrFail($id);
        $errors = (new AttendanceEditService())->update($record, $_POST);

        if ($errors) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect("/employee/attendance/{$record['id']}/edit");
        }

        flash('success', 'Marca modificada. El cambio y tu justificacion quedaron registrados.');
        redirect('/employee/attendance');
    }

    private function findOwnRecordOrFail(string $id): array
    {
        $record = AttendanceRecord::findForEmployee((int) $id, (int) Auth::employee()['id']);
        if (!$record) {
            flash('error', 'Marca no encontrada.');
            redirect('/employee/attendance');
        }

        return $record;
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
