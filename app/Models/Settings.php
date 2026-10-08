<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Tenant;

/** Configuracion de la empresa activa (una fila por empresa). app_name es el nombre de la empresa. */
class Settings
{
    /** Monedas disponibles: codigo => [nombre, simbolo]. */
    public const CURRENCIES = [
        'CRC' => ['name' => 'Colones', 'symbol' => '₡'],
        'USD' => ['name' => 'Dolares', 'symbol' => '$'],
        'EUR' => ['name' => 'Euros', 'symbol' => '€'],
    ];

    /** @var array<int,array> Cache por empresa. */
    private static array $cache = [];

    public static function get(): array
    {
        $companyId = Tenant::id();

        if (!isset(self::$cache[$companyId])) {
            $stmt = Database::connection()->prepare(
                'SELECT settings.*, companies.name AS app_name
                 FROM settings JOIN companies ON companies.id = settings.company_id
                 WHERE settings.company_id = ?'
            );
            $stmt->execute([$companyId]);
            self::$cache[$companyId] = $stmt->fetch() ?: self::defaults();
        }

        return self::$cache[$companyId];
    }

    private static function defaults(): array
    {
        return [
            'app_name' => Tenant::company()['name'] ?? 'Time Tracking',
            'logo_path' => null,
            'currency' => 'USD',
            'attendance_mode' => 'login',
            'allowed_ips' => null,
            'is_24_7' => 0,
            'same_hours_every_day' => 1,
            'open_time' => '08:00:00',
            'close_time' => '17:00:00',
        ];
    }

    /** Fila de configuracion inicial para una empresa nueva (valores por defecto de la tabla). */
    public static function createFor(int $companyId): void
    {
        $stmt = Database::connection()->prepare('INSERT INTO settings (company_id) VALUES (?)');
        $stmt->execute([$companyId]);
    }

    public static function logoPathFor(int $companyId): ?string
    {
        $stmt = Database::connection()->prepare('SELECT logo_path FROM settings WHERE company_id = ?');
        $stmt->execute([$companyId]);
        return $stmt->fetch()['logo_path'] ?? null;
    }

    public static function updateGeneral(string $appName, ?string $logoPath, string $currency): void
    {
        Company::rename(Tenant::id(), $appName);

        $stmt = Database::connection()->prepare('UPDATE settings SET logo_path = ?, currency = ? WHERE company_id = ?');
        $stmt->execute([$logoPath, $currency, Tenant::id()]);
        self::$cache = [];
    }

    /** Red autorizada de una empresa (usado en el login, antes de que exista empresa activa). */
    public static function allowedIpsFor(int $companyId): ?string
    {
        $stmt = Database::connection()->prepare('SELECT allowed_ips FROM settings WHERE company_id = ?');
        $stmt->execute([$companyId]);
        return $stmt->fetch()['allowed_ips'] ?? null;
    }

    /** @param string[] $rules IPs o rangos CIDR ya validados; vacio = sin restriccion. */
    public static function updateAllowedIps(array $rules): void
    {
        $stmt = Database::connection()->prepare('UPDATE settings SET allowed_ips = ? WHERE company_id = ?');
        $stmt->execute([$rules ? implode("\n", $rules) : null, Tenant::id()]);
        self::$cache = [];
    }

    public static function updateAttendanceMode(string $mode): void
    {
        $stmt = Database::connection()->prepare('UPDATE settings SET attendance_mode = ? WHERE company_id = ?');
        $stmt->execute([$mode, Tenant::id()]);
        self::$cache = [];
    }

    public static function updateBusinessHours(bool $is24h, bool $sameEveryDay, ?string $openTime, ?string $closeTime): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE settings SET is_24_7 = ?, same_hours_every_day = ?, open_time = ?, close_time = ? WHERE company_id = ?'
        );
        $stmt->execute([
            $is24h ? 1 : 0,
            $sameEveryDay ? 1 : 0,
            $openTime ?: '00:00:00',
            $closeTime ?: '23:59:00',
            Tenant::id(),
        ]);
        self::$cache = [];
    }
}
