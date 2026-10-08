<?php

namespace App\Controllers;

use App\Core\Tenant;
use App\Models\AttendanceRecord;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Settings;
use App\Services\AttendanceService;
use App\Support\IpAccess;

/**
 * Kiosco publico por empresa (/kiosk/{codigo-empresa}): el empleado se identifica con su
 * numero de empleado y los ultimos 5 digitos de su cedula, y luego marca entrada o salida.
 */
class KioskController
{
    /** Segundos que dura la verificacion del empleado para pulsar entrada/salida. */
    private const VERIFICATION_TTL = 120;

    /** Pantalla para escribir el codigo de empresa cuando se entra a /kiosk sin el. */
    public function select(): void
    {
        $slug = trim($_GET['company'] ?? '');
        if ($slug !== '') {
            redirect('/kiosk/' . rawurlencode(strtolower($slug)));
        }

        view('kiosk/select', ['title' => 'Marcar asistencia', 'layout' => 'layouts/guest']);
    }

    public function show(string $slug): void
    {
        $this->enterCompany($slug);
        unset($_SESSION['kiosk_verified']);
        view('kiosk/index', ['title' => 'Marcar asistencia', 'layout' => 'layouts/guest', 'kioskUrl' => $this->kioskUrl()]);
    }

    /** Verifica numero de empleado + cedula y deja al empleado listo para marcar. */
    public function lookup(string $slug): void
    {
        $this->enterCompany($slug);
        $employee = $this->verifyCredentialsOrFail();

        $_SESSION['kiosk_verified'] = [
            'company_id' => Tenant::id(),
            'employee_id' => (int) $employee['id'],
            'expires_at' => time() + self::VERIFICATION_TTL,
        ];

        view('kiosk/index', [
            'title' => 'Marcar asistencia',
            'layout' => 'layouts/guest',
            'kioskUrl' => $this->kioskUrl(),
            'employee' => $employee,
            'openRecord' => AttendanceRecord::openFor($employee['id']),
            'returnAfterSeconds' => self::VERIFICATION_TTL,
        ]);
    }

    /** Marca la entrada y vuelve a la pantalla principal del kiosco con el resultado. */
    public function clockIn(string $slug): void
    {
        $this->enterCompany($slug);
        $employee = $this->verifiedEmployeeOrFail();

        $result = (new AttendanceService())->clockIn($employee['id']);
        if ($result['success']) {
            flash('success', sprintf(
                'Entrada registrada con exito. %s, hora de entrada: %s.',
                $employee['full_name'],
                format_time($result['clock_in'])
            ));
        } else {
            flash('error', $result['message']);
        }

        redirect($this->kioskUrl());
    }

    /** Marca la salida y vuelve a la pantalla principal del kiosco con el resultado. */
    public function clockOut(string $slug): void
    {
        $this->enterCompany($slug);
        $employee = $this->verifiedEmployeeOrFail();

        $result = (new AttendanceService())->clockOut($employee['id']);
        if ($result['success']) {
            flash('success', sprintf(
                'Salida registrada con exito. %s, entrada: %s, salida: %s, horas trabajadas: %s.',
                $employee['full_name'],
                format_time($result['clock_in']),
                format_time($result['clock_out']),
                format_hours((float) $result['hours_worked'])
            ));
        } else {
            flash('error', $result['message']);
        }

        redirect($this->kioskUrl());
    }

    private function verifyCredentialsOrFail(): array
    {
        $number = trim($_POST['employee_number'] ?? '');
        $pin = trim($_POST['document_pin'] ?? '');
        $employee = $number !== '' ? Employee::findByNumber($number) : null;
        $expectedPin = $employee ? Employee::kioskPin($employee['document_id']) : null;

        if (!$employee || $expectedPin === null || !hash_equals($expectedPin, $pin)) {
            flash('error', 'Codigo de empleado o cedula incorrectos, o empleado inactivo.');
            redirect($this->kioskUrl());
        }

        return $employee;
    }

    /** Empleado verificado en esta sesion; la verificacion se consume al marcar (una sola accion). */
    private function verifiedEmployeeOrFail(): array
    {
        $verified = $_SESSION['kiosk_verified'] ?? null;
        unset($_SESSION['kiosk_verified']);

        $isValid = $verified
            && $verified['company_id'] === Tenant::id()
            && $verified['expires_at'] >= time();
        $employee = $isValid ? Employee::find($verified['employee_id']) : null;

        if (!$employee || $employee['status'] !== 'active') {
            flash('error', 'La verificacion expiro. Ingresa de nuevo tu codigo y cedula.');
            redirect($this->kioskUrl());
        }

        return $employee;
    }

    /**
     * Activa la empresa del kiosco; debe existir, estar activa, tener el modo kiosco
     * habilitado y la peticion debe venir de su red autorizada (si la configuro).
     */
    private function enterCompany(string $slug): void
    {
        $company = Company::findBySlug($slug);

        if (!$company || $company['status'] !== 'active') {
            flash('error', 'Codigo de empresa invalido o inactivo.');
            redirect('/kiosk');
        }

        Tenant::set($company);
        $settings = Settings::get();

        if ($settings['attendance_mode'] !== 'kiosk') {
            flash('error', 'El registro con codigo de empleado no esta habilitado para esta empresa.');
            redirect('/kiosk');
        }

        if (!IpAccess::clientAllowed($settings['allowed_ips'] ?? null)) {
            flash('error', 'Este kiosco solo funciona desde la red autorizada de la empresa.');
            redirect('/kiosk');
        }
    }

    private function kioskUrl(): string
    {
        return '/kiosk/' . rawurlencode(Tenant::company()['slug']);
    }
}
