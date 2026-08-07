<?php

namespace App\Controllers\Admin;

use App\Models\BusinessHours;
use App\Models\Settings;

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
        ]);
    }

    public function updateGeneral(): void
    {
        $appName = trim($_POST['app_name'] ?? '');
        if ($appName === '') {
            flash('error', 'El nombre del sitio es obligatorio.');
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

        Settings::updateGeneral($appName, $logoPath);
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
