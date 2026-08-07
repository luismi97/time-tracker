<?php

namespace App\Services;

use FPDF;

/** Genera el PDF de reporte de horas y pago usando FPDF (una seccion por empleado). */
class PdfReportService
{
    private PayrollService $payroll;

    public function __construct(?PayrollService $payroll = null)
    {
        $this->payroll = $payroll ?? new PayrollService();
    }

    public function build(array $employees, array $recordsByEmployee, string $start, string $end): FPDF
    {
        $pdf = new FPDF();
        $pdf->SetTitle('Reporte de Horas y Pago');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 20);

        foreach ($employees as $employee) {
            $records = $recordsByEmployee[$employee['id']] ?? [];
            $this->addEmployeeSection($pdf, $employee, $records, $start, $end);
        }

        return $pdf;
    }

    private function addEmployeeSection(FPDF $pdf, array $employee, array $records, string $start, string $end): void
    {
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Reporte de Horas y Pago', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'Periodo: ' . $start . ' a ' . $end, 0, 1, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Informacion del empleado', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'Numero de empleado: #' . $employee['employee_number'], 0, 1);
        $pdf->Cell(0, 6, 'Nombre: ' . $employee['full_name'], 0, 1);
        $pdf->Cell(0, 6, 'Correo: ' . $employee['email'], 0, 1);
        $pdf->Cell(0, 6, 'Telefono: ' . ($employee['phone'] ?: 'N/A'), 0, 1);
        $pdf->Cell(0, 6, 'Direccion: ' . ($employee['address'] ?: 'N/A'), 0, 1);
        $pdf->Cell(0, 6, 'Salario por hora: $' . number_format((float) $employee['hourly_rate'], 2), 0, 1);
        $pdf->Cell(0, 6, 'Paga horas extra (1.5x): ' . (!empty($employee['overtime_paid']) ? 'Si' : 'No'), 0, 1);
        $pdf->Cell(0, 6, 'Hora de almuerzo (no se paga): ' . (!empty($employee['has_lunch_break']) ? 'Si' : 'No'), 0, 1);
        $pdf->Ln(4);

        $widths = [28, 28, 28, 22, 22, 27];
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        foreach (['Fecha', 'Entrada', 'Salida', 'Horas', 'Extra', 'Estado'] as $i => $header) {
            $pdf->Cell($widths[$i], 8, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);
        foreach ($records as $record) {
            $pdf->Cell($widths[0], 7, $record['work_date'], 1, 0, 'C');
            $pdf->Cell($widths[1], 7, $record['clock_in'] ? substr($record['clock_in'], 11, 5) : '-', 1, 0, 'C');
            $pdf->Cell($widths[2], 7, $record['clock_out'] ? substr($record['clock_out'], 11, 5) : '-', 1, 0, 'C');
            $pdf->Cell($widths[3], 7, $record['hours_worked'] !== null ? number_format((float) $record['hours_worked'], 2) : '-', 1, 0, 'C');
            $pdf->Cell($widths[4], 7, $record['overtime_hours'] !== null ? number_format((float) $record['overtime_hours'], 2) : '-', 1, 0, 'C');
            $pdf->Cell($widths[5], 7, $record['status'] === 'closed' ? 'Cerrado' : 'Abierto', 1, 0, 'C');
            $pdf->Ln();
        }

        if (!$records) {
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->Cell(array_sum($widths), 8, 'Sin registros en el periodo seleccionado.', 1, 1, 'C');
        }

        $summary = $this->payroll->summarize($records, $employee);
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 7, 'Total de horas trabajadas: ' . number_format($summary['total_hours'], 2), 0, 1);
        $pdf->Cell(0, 7, 'Total de horas extra: ' . number_format($summary['total_overtime'], 2), 0, 1);
        $pdf->Cell(0, 7, 'Total de horas pagadas: ' . number_format($summary['total_paid_hours'], 2), 0, 1);
        $pdf->Cell(0, 7, 'Total a pagar: $' . number_format($summary['total_pay'], 2), 0, 1);
    }
}
