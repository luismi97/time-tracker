<?php

namespace App\Controllers\Admin;

use App\Core\Tenant;
use App\Models\BusinessHours;
use App\Models\Settings;
use App\Support\IpAccess;

class SettingsController
{
    private const ALLOWED_LOGO_TYPES = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
    ];
    private const MAX_LOGO_SIZE = 2 * 1024 * 1024;

    public function index(): void
    {
        view('admin/settings/index', [
            'title' => 'Configuracion',
            'settings' => Settings::get(),
            'businessHours' => BusinessHours::all(),
            'kioskUrl' => '/kiosk/' . rawurlencode(Tenant::company()['slug']),
            'clientIp' => IpAccess::clientIp(),
        ]);
    }

    public function updateGeneral(): void
    {
        $appName = trim($_POST['app_name'] ?? '');
        if ($appName === '') {
            flash('error', 'El nombre de la empresa es obligatorio.');
            redirect('/admin/settings');
        }

        $logoPath = Settings::get()['logo_path'];

        if (!empty($_FILES['logo']['name'])) {
            $uploaded = $this->handleLogoUpload();
            if ($uploaded === false) {
                redirect('/admin/settings');
            }
            $logoPath = $uploaded;
        }

        $currency = $_POST['currency'] ?? 'USD';
        if (!isset(Settings::CURRENCIES[$currency])) {
            $currency = 'USD';
        }

        Settings::updateGeneral($appName, $logoPath, $currency);
        flash('success', 'Configuracion general actualizada.');
        redirect('/admin/settings');
    }

    public function updateAttendanceMode(): void
    {
        $mode = $_POST['attendance_mode'] ?? 'login';
        if (!in_array($mode, ['login', 'kiosk'], true)) {
            $mode = 'login';
        }

        Settings::updateAttendanceMode($mode);
        flash('success', 'Modo de registro de horas actualizado.');
        redirect('/admin/settings');
    }

    /** Red autorizada desde donde los empleados pueden iniciar sesion y usar el kiosco. */
    public function updateNetwork(): void
    {
        $rules = array_values(array_unique(IpAccess::parse($_POST['allowed_ips'] ?? '')));
        $invalid = array_filter($rules, fn (string $rule) => !IpAccess::isValidRule($rule));

        if ($invalid) {
            flash('error', 'Direcciones no validas: ' . implode(', ', $invalid) . '. Usa una IP (190.10.20.30) o un rango CIDR (190.10.20.0/24).');
            redirect('/admin/settings');
        }

        Settings::updateAllowedIps($rules);
        flash('success', $rules
            ? 'Red autorizada actualizada. Los empleados solo podran entrar desde esas direcciones.'
            : 'Restriccion de red desactivada: los empleados pueden entrar desde cualquier red.');
        redirect('/admin/settings');
    }

    public function updateBusinessHours(): void
    {
        $is24h = isset($_POST['is_24_7_hours']);
        $is7Days = isset($_POST['same_hours_every_day']);

        Settings::updateBusinessHours(
            $is24h,
            $is7Days,
            $_POST['open_time'] ?? null,
            $_POST['close_time'] ?? null
        );

        if (!$is24h && !$is7Days) {
            $days = [];
            for ($day = 0; $day <= 6; $day++) {
                $days[$day] = [
                    'is_open' => isset($_POST['day_open'][$day]),
                    'open_time' => $_POST['day_open_time'][$day] ?? null,
                    'close_time' => $_POST['day_close_time'][$day] ?? null,
                ];
            }
            BusinessHours::updateAll($days);
        }

        flash('success', 'Horario del negocio actualizado.');
        redirect('/admin/settings');
    }

    /** @return string|false Ruta publica del logo guardado, o false si la subida fallo. */
    private function handleLogoUpload(): string|false
    {
        $file = $_FILES['logo'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'No se pudo subir el logo.');
            return false;
        }

        if ($file['size'] > self::MAX_LOGO_SIZE) {
            flash('error', 'El logo no puede superar 2MB.');
            return false;
        }

        $mimeType = mime_content_type($file['tmp_name']);
        if (!isset(self::ALLOWED_LOGO_TYPES[$mimeType])) {
            flash('error', 'Formato de logo no permitido. Usa PNG, JPG, WEBP o SVG.');
            return false;
        }

        $uploadDir = BASE_PATH . '/public/assets/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $filename = 'logo-' . bin2hex(random_bytes(8)) . '.' . self::ALLOWED_LOGO_TYPES[$mimeType];

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
            flash('error', 'No se pudo guardar el logo.');
            return false;
        }

        $this->deletePreviousLogo(Settings::get()['logo_path']);

        return '/assets/uploads/' . $filename;
    }

    private function deletePreviousLogo(?string $logoPath): void
    {
        if (!$logoPath) {
            return;
        }

        $path = BASE_PATH . '/public' . $logoPath;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
