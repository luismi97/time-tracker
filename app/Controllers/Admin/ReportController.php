<?php

namespace App\Controllers\Admin;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Services\PayrollService;
use App\Services\PdfReportService;
use App\Support\DateRange;

class ReportController
{
    public function create(): void
    {
        view('admin/reports/index', ['title' => 'Reportes', 'employees' => Employee::all(), 'formValues' => $this->defaultFormValues()]);
    }

    /** Vista previa en tabla de los datos antes de exportar el PDF. */
    public function preview(): void
    {
        [$employees, $recordsByEmployee, $startStr, $endStr] = $this->resolveReportData();

        if (!$employees) {
            flash('error', 'No se encontro informacion para generar la vista previa.');
            redirect('/admin/reports');
        }

        $payroll = new PayrollService();
        $previewRows = [];
        foreach ($employees as $employee) {
            $previewRows[] = [
                'employee' => $employee,
                'summary' => $payroll->summarize($recordsByEmployee[$employee['id']] ?? [], $employee),
            ];
        }

        view('admin/reports/index', [
            'title' => 'Reportes',
            'employees' => Employee::all(),
            'formValues' => $this->formValuesFromRequest(),
            'previewRows' => $previewRows,
            'previewStart' => $startStr,
            'previewEnd' => $endStr,
        ]);
    }

    public function generate(): void
    {
        [$employees, $recordsByEmployee, $startStr, $endStr] = $this->resolveReportData();

        if (!$employees) {
            flash('error', 'No se encontro informacion para generar el reporte.');
            redirect('/admin/reports');
        }

        $pdf = (new PdfReportService())->build($employees, $recordsByEmployee, $startStr, $endStr);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="reporte-' . $startStr . '-a-' . $endStr . '.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $pdf->Output('S');
    }

    private function resolveReportData(): array
    {
        $period = $_POST['period'] ?? 'day';
        [$start, $end] = DateRange::resolve($period, $_POST);
        $startStr = $start->format('Y-m-d');
        $endStr = $end->format('Y-m-d');

        $employeeId = $_POST['employee_id'] ?? 'all';
        $employees = $employeeId === 'all'
            ? Employee::all()
            : array_filter([Employee::find((int) $employeeId)]);

        $recordsByEmployee = [];
        foreach ($employees as $employee) {
            $recordsByEmployee[$employee['id']] = AttendanceRecord::forEmployeeInRange((int) $employee['id'], $startStr, $endStr);
        }

        return [$employees, $recordsByEmployee, $startStr, $endStr];
    }

    private function formValuesFromRequest(): array
    {
        return [
            'employee_id' => $_POST['employee_id'] ?? 'all',
            'period' => $_POST['period'] ?? 'day',
            'date' => $_POST['date'] ?? date('Y-m-d'),
            'year' => $_POST['year'] ?? date('Y'),
            'month' => $_POST['month'] ?? date('n'),
            'from' => $_POST['from'] ?? '',
            'to' => $_POST['to'] ?? '',
        ];
    }

    private function defaultFormValues(): array
    {
        return [
            'employee_id' => 'all',
            'period' => 'day',
            'date' => date('Y-m-d'),
            'year' => date('Y'),
            'month' => date('n'),
            'from' => '',
            'to' => '',
        ];
    }
}
