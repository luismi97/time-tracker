<?php

namespace App\Support;

/**
 * Restriccion de red por empresa: decide si la IP del cliente pertenece a alguna de las
 * IPs o rangos CIDR autorizados (IPv4 o IPv6). Una lista vacia no restringe nada.
 */
class IpAccess
{
    /**
     * IP real del cliente. Si la peticion llega desde un proxy de confianza (en Cloudways,
     * Nginx/Varnish en el mismo servidor) se toma de X-Forwarded-For, recorriendolo de
     * derecha a izquierda para no confiar en valores que el propio cliente haya inyectado.
     */
    public static function clientIp(): string
    {
        $remote = $_SERVER['REMOTE_ADDR'] ?? '';
        $trusted = self::parse($_ENV['TRUSTED_PROXIES'] ?? '127.0.0.1,::1');

        if (!$trusted || !self::allowed($remote, $trusted)) {
            return $remote;
        }

        $forwarded = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
        for ($i = count($forwarded) - 1; $i >= 0; $i--) {
            $ip = $forwarded[$i];
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                break;
            }
            if (!self::allowed($ip, $trusted)) {
                return $ip;
            }
        }

        $realIp = trim($_SERVER['HTTP_X_REAL_IP'] ?? '');
        return filter_var($realIp, FILTER_VALIDATE_IP) ? $realIp : $remote;
    }

    /** Separa una lista de reglas escrita por el usuario (lineas, comas o espacios). */
    public static function parse(?string $text): array
    {
        return preg_split('/[\s,]+/', (string) $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    public static function isValidRule(string $rule): bool
    {
        [$address, $bits] = array_pad(explode('/', $rule, 2), 2, null);
        $binary = self::toBinary($address);

        if ($binary === null) {
            return false;
        }

        return $bits === null || (ctype_digit($bits) && (int) $bits <= strlen($binary) * 8);
    }

    /** true si no hay reglas, o si la IP coincide con alguna. */
    public static function allowed(string $ip, array $rules): bool
    {
        if (!$rules) {
            return true;
        }

        foreach ($rules as $rule) {
            if (self::matches($ip, $rule)) {
                return true;
            }
        }

        return false;
    }

    /** IP del cliente permitida segun la lista guardada de una empresa (settings.allowed_ips). */
    public static function clientAllowed(?string $allowedIps): bool
    {
        return self::allowed(self::clientIp(), self::parse($allowedIps));
    }

    private static function matches(string $ip, string $rule): bool
    {
        [$address, $bits] = array_pad(explode('/', $rule, 2), 2, null);
        $ipBinary = self::toBinary($ip);
        $netBinary = self::toBinary($address);

        if ($ipBinary === null || $netBinary === null || strlen($ipBinary) !== strlen($netBinary)) {
            return false;
        }

        $bits = $bits === null ? strlen($netBinary) * 8 : (int) $bits;
        $fullBytes = intdiv($bits, 8);
        $remainingBits = $bits % 8;

        if (substr($ipBinary, 0, $fullBytes) !== substr($netBinary, 0, $fullBytes)) {
            return false;
        }

        if ($remainingBits === 0) {
            return true;
        }

        $mask = chr((0xFF << (8 - $remainingBits)) & 0xFF);
        return ($ipBinary[$fullBytes] & $mask) === ($netBinary[$fullBytes] & $mask);
    }

    /** Representacion binaria de la IP; las IPv4 mapeadas en IPv6 (::ffff:a.b.c.d) se tratan como IPv4. */
    private static function toBinary(?string $ip): ?string
    {
        if ($ip === null || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        $binary = inet_pton($ip);
        if (strlen($binary) === 16 && str_starts_with($binary, str_repeat("\0", 10) . "\xff\xff")) {
            return substr($binary, 12);
        }

        return $binary;
    }
}
