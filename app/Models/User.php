<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT users.*, roles.name AS role_name FROM users
             JOIN roles ON roles.id = users.role_id
             WHERE users.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT users.*, roles.name AS role_name FROM users
             JOIN roles ON roles.id = users.role_id
             WHERE users.email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM users WHERE email = ?';
        $params = [$email];

        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    }

    public static function create(string $email, string $password, int $roleId): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (role_id, email, password_hash, is_active) VALUES (?, ?, ?, 1)'
        );
        $stmt->execute([$roleId, $email, password_hash($password, PASSWORD_BCRYPT)]);
        return (int) Database::connection()->lastInsertId();
    }

    public static function updateCredentials(int $id, string $email, ?string $password): void
    {
        if ($password) {
            $stmt = Database::connection()->prepare('UPDATE users SET email = ?, password_hash = ? WHERE id = ?');
            $stmt->execute([$email, password_hash($password, PASSWORD_BCRYPT), $id]);
            return;
        }

        $stmt = Database::connection()->prepare('UPDATE users SET email = ? WHERE id = ?');
        $stmt->execute([$email, $id]);
    }

    public static function setActive(int $id, bool $active): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $stmt->execute([$active ? 1 : 0, $id]);
    }

    public static function touchLastLogin(int $id): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function recentLogins(int $limit = 5): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT users.email, users.last_login_at, employees.full_name, roles.name AS role_name
             FROM users
             LEFT JOIN employees ON employees.user_id = users.id
             JOIN roles ON roles.id = users.role_id
             WHERE users.last_login_at IS NOT NULL
             ORDER BY users.last_login_at DESC
             LIMIT " . (int) $limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
