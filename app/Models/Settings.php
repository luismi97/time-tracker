<?php

namespace App\Models;

use App\Core\Database;

/** Configuracion global de la app (fila unica, id=1). */
class Settings
{
    private static ?array $cache = null;

    public static function get(): array
    {
        if (self::$cache === null) {
            $stmt = Database::connection()->query('SELECT * FROM settings WHERE id = 1');
            self::$cache = $stmt->fetch() ?: self::defaults();
        }

        return self::$cache;
    }

    private static function defaults(): array
    {
        return [
            'app_name' => 'Time Tracking',
            'logo_path' => null,
            'attendance_mode' => 'login',
            'is_24_7' => 0,
            'same_hours_every_day' => 1,
            'open_time' => '08:00:00',
            'close_time' => '17:00:00',
        ];
    }

    public static function updateGeneral(string $appName, ?string $logoPath): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE settings SET app_name = ?, logo_path = ? WHERE id = 1'
        );
        $stmt->execute([$appName, $logoPath]);
        self::$cache = null;
    }

    public static function updateAttendanceMode(string $mode): void
    {
        $stmt = Database::connection()->prepare('UPDATE settings SET attendance_mode = ? WHERE id = 1');
        $stmt->execute([$mode]);
        self::$cache = null;
    }

    public static function updateBusinessHours(bool $is24h, bool $sameEveryDay, ?string $openTime, ?string $closeTime): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE settings SET is_24_7 = ?, same_hours_every_day = ?, open_time = ?, close_time = ? WHERE id = 1'
        );
        $stmt->execute([
            $is24h ? 1 : 0,
            $sameEveryDay ? 1 : 0,
            $openTime ?: '00:00:00',
            $closeTime ?: '23:59:00',
        ]);
        self::$cache = null;
    }
}
