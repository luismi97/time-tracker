<?php

namespace App\Core;

use RuntimeException;

/**
 * Empresa activa de la peticion actual. Todos los modelos con datos de empresa
 * (empleados, asistencia, configuracion, horario) filtran por Tenant::id().
 */
class Tenant
{
    private static ?array $company = null;

    public static function set(array $company): void
    {
        self::$company = $company;
    }

    public static function check(): bool
    {
        return self::$company !== null;
    }

    public static function company(): ?array
    {
        return self::$company;
    }

    public static function id(): int
    {
        if (self::$company === null) {
            throw new RuntimeException('No hay una empresa activa para esta peticion.');
        }

        return (int) self::$company['id'];
    }
}
