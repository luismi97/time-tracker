<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Tenant;

/** Horario manual por dia de la semana (0=Domingo ... 6=Sabado) de la empresa activa. */
class BusinessHours
{
    public static function all(): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM business_hours WHERE company_id = ? ORDER BY day_of_week ASC');
        $stmt->execute([Tenant::id()]);
        return $stmt->fetchAll();
    }

    /** Horario inicial de una empresa nueva: lunes a viernes 08:00-17:00. */
    public static function createFor(int $companyId): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO business_hours (company_id, day_of_week, is_open, open_time, close_time) VALUES (?, ?, ?, ?, ?)'
        );

        for ($day = 0; $day <= 6; $day++) {
            $isOpen = $day >= 1 && $day <= 5;
            $stmt->execute([$companyId, $day, $isOpen ? 1 : 0, $isOpen ? '08:00:00' : null, $isOpen ? '17:00:00' : null]);
        }
    }

    public static function updateAll(array $days): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'UPDATE business_hours SET is_open = ?, open_time = ?, close_time = ? WHERE company_id = ? AND day_of_week = ?'
            );

            for ($day = 0; $day <= 6; $day++) {
                $config = $days[$day] ?? [];
                $isOpen = !empty($config['is_open']);
                $stmt->execute([
                    $isOpen ? 1 : 0,
                    $isOpen ? ($config['open_time'] ?: '08:00:00') : null,
                    $isOpen ? ($config['close_time'] ?: '17:00:00') : null,
                    Tenant::id(),
                    $day,
                ]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
