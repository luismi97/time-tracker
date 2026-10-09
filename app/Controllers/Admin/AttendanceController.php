<?php

namespace App\Controllers\Admin;

use App\Core\Paginator;
use App\Models\AttendanceEdit;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Services\AttendanceEditService;
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

    /** El administrador corrige una marca de su empresa; queda registrado quien y por que. */
    public function edit(string $id): void
    {
        $record = $this->findOrFail($id);

        view('admin/attendance/edit', [
            'title' => 'Modificar marca',
            'record' => $record,
            'edits' => AttendanceEdit::forRecord((int) $record['id']),
        ]);
    }

    public function update(string $id): void
    {
        $record = $this->findOrFail($id);
        $errors = (new AttendanceEditService())->update($record, $_POST);

        if ($errors) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect("/admin/attendance/{$record['id']}/edit");
        }

        flash('success', 'Marca de ' . $record['full_name'] . ' modificada y registrada en el historial.');
        redirect("/admin/attendance/{$record['id']}/edit");
    }

    private function findOrFail(string $id): array
    {
        $record = AttendanceRecord::findForCompany((int) $id);
        if (!$record) {
            flash('error', 'Marca no encontrada.');
            redirect('/admin/attendance');
        }

        return $record;
    }
}
