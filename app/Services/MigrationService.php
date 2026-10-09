<?php

namespace App\Services;

use App\Core\Database;
use PDO;

/**
 * Ejecuta los archivos de database/migrations en orden y registra cada uno en la tabla
 * `migrations` para no repetirlo. Pensado para servidores sin acceso comodo a la consola.
 */
class MigrationService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    /** @return array{applied: string[], pending: string[]} */
    public function status(): array
    {
        $this->ensureTable();
        $applied = $this->applied();

        return [
            'applied' => array_values(array_intersect($this->files(), $applied)),
            'pending' => array_values(array_diff($this->files(), $applied)),
        ];
    }

    /**
     * Ejecuta las migraciones pendientes en orden y se detiene en la primera que falle.
     *
     * @return array{ran: string[], failed: ?string, error: ?string}
     */
    public function runPending(): array
    {
        $ran = [];

        foreach ($this->status()['pending'] as $file) {
            try {
                foreach ($this->statements(file_get_contents($this->path($file))) as $statement) {
                    $this->pdo->exec($statement);
                }
            } catch (\Throwable $e) {
                return ['ran' => $ran, 'failed' => $file, 'error' => $e->getMessage()];
            }

            $this->markApplied($file);
            $ran[] = $file;
        }

        return ['ran' => $ran, 'failed' => null, 'error' => null];
    }

    /** Archivos .sql de la carpeta de migraciones, ordenados por nombre. */
    private function files(): array
    {
        $files = array_map('basename', glob(BASE_PATH . '/database/migrations/*.sql') ?: []);
        sort($files);
        return $files;
    }

    private function path(string $file): string
    {
        return BASE_PATH . '/database/migrations/' . $file;
    }

    private function applied(): array
    {
        return $this->pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
    }

    private function markApplied(string $file): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO migrations (migration) VALUES (?)');
        $stmt->execute([$file]);
    }

    /** Crea la tabla de control; si no existia, registra lo que ya estaba aplicado a mano. */
    private function ensureTable(): void
    {
        if ($this->tableExists('migrations')) {
            return;
        }

        $this->pdo->exec(
            'CREATE TABLE migrations (
                migration VARCHAR(255) NOT NULL PRIMARY KEY,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        foreach ($this->files() as $file) {
            if ($this->wasAppliedManually($file)) {
                $this->markApplied($file);
            }
        }
    }

    /**
     * Instalaciones anteriores a esta herramienta importaron los .sql a mano (o via Docker):
     * se detecta cada migracion existente por el cambio que hace en el esquema o los datos.
     */
    private function wasAppliedManually(string $file): bool
    {
        return match (substr($file, 0, 3)) {
            '001' => $this->tableExists('roles'),
            '002' => $this->tableExists('users'),
            '003' => $this->tableExists('employees'),
            '004' => $this->tableExists('attendance_records'),
            '005' => $this->tableExists('roles') && $this->count('SELECT COUNT(*) FROM roles') > 0,
            '006' => $this->tableExists('users')
                && $this->count("SELECT COUNT(*) FROM users WHERE email = 'ana.rodriguez@timetracking.test'") > 0,
            '007' => $this->columnExists('employees', 'overtime_paid'),
            '008' => $this->tableExists('settings'),
            '009' => $this->tableExists('business_hours'),
            '010' => $this->tableExists('companies'),
            '011' => $this->columnExists('settings', 'allowed_ips'),
            '012' => $this->columnExists('settings', 'currency'),
            '013' => $this->columnExists('employees', 'is_paid'),
            '014' => $this->tableExists('attendance_edits'),
            default => false,
        };
    }

    private function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?'
        );
        $stmt->execute([$table]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }

    private function count(string $sql): int
    {
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    /**
     * Divide un archivo SQL en sentencias (por ";"), ignorando comentarios y respetando
     * los ";" dentro de textos. Se ejecutan una a una para que cualquier error se detecte.
     */
    private function statements(string $sql): array
    {
        $statements = [];
        $current = '';
        $quote = null;
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $sql[$i + 1] ?? '';

            if ($quote !== null) {
                $current .= $char;
                if ($char === '\\') {
                    $current .= $next;
                    $i++;
                } elseif ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if ($char === '-' && $next === '-') {
                // Salta hasta el salto de linea, que se conserva como separador.
                $i = ($end = strpos($sql, "\n", $i)) === false ? $length : $end - 1;
                continue;
            }

            if ($char === '/' && $next === '*') {
                $i = ($end = strpos($sql, '*/', $i + 2)) === false ? $length : $end + 1;
                continue;
            }

            if ($char === "'" || $char === '"' || $char === '`') {
                $quote = $char;
            }

            if ($char === ';') {
                $statements[] = trim($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $statements[] = trim($current);

        return array_values(array_filter($statements, fn (string $statement) => $statement !== ''));
    }
}
