<?php

namespace App\Models;

use App\Core\Database;

/** Horario manual por dia de la semana (0=Domingo ... 6=Sabado). */
class BusinessHours
{
    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM business_hours ORDER BY day_of_week ASC');
        return $stmt->fetchAll();
    }

    public static function updateAll(array $days): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'UPDATE business_hours SET is_open = ?, open_time = ?, close_time = ? WHERE day_of_week = ?'
            );

            for ($day = 0; $day <= 6; $day++) {
                $config = $days[$day] ?? [];
                $isOpen = !empty($config['is_open']);
                $stmt->execute([
                    $isOpen ? 1 : 0,
                    $isOpen ? ($config['open_time'] ?: '08:00:00') : null,
                    $isOpen ? ($config['close_time'] ?: '17:00:00') : null,
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
