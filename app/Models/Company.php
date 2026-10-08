<?php

namespace App\Models;

use App\Core\Database;

class Company
{
    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM companies WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM companies WHERE slug = ?');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    /** Todas las empresas con conteo de empleados y administradores (panel del super admin). */
    public static function allWithStats(): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT companies.*,
                (SELECT COUNT(*) FROM employees WHERE employees.company_id = companies.id) AS employees_count,
                (SELECT COUNT(*) FROM employees WHERE employees.company_id = companies.id AND employees.status = 'active') AS active_employees_count,
                (SELECT COUNT(*) FROM users WHERE users.company_id = companies.id AND users.role_id = ?) AS admins_count
             FROM companies ORDER BY companies.name ASC"
        );
        $stmt->execute([Role::ADMIN]);
        return $stmt->fetchAll();
    }

    public static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM companies WHERE slug = ?';
        $params = [$slug];

        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    }

    public static function create(string $name, string $slug, string $status): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO companies (name, slug, status) VALUES (?, ?, ?)');
        $stmt->execute([$name, $slug, $status]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function update(int $id, string $name, string $slug, string $status): void
    {
        $stmt = Database::connection()->prepare('UPDATE companies SET name = ?, slug = ?, status = ? WHERE id = ?');
        $stmt->execute([$name, $slug, $status, $id]);
    }

    public static function rename(int $id, string $name): void
    {
        $stmt = Database::connection()->prepare('UPDATE companies SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
    }

    /** Elimina la empresa; usuarios, empleados, asistencia, configuracion y horario se eliminan en cascada. */
    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM companies WHERE id = ?');
        $stmt->execute([$id]);
    }
}
