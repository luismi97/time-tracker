<?php

namespace App\Core;

use App\Models\Employee;
use App\Models\User;

class Auth
{
    private static ?array $user = null;

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);

        if (!$user || !$user['is_active'] || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        Session::regenerate();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role_name'];
        User::touchLastLogin($user['id']);
        self::$user = null;

        return true;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isEmployee(): bool
    {
        return self::role() === 'employee';
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        if (self::$user === null) {
            self::$user = User::find(self::id());
        }

        return self::$user;
    }

    /** Registro de empleado asociado al usuario autenticado (null si es admin). */
    public static function employee(): ?array
    {
        if (!self::isEmployee()) {
            return null;
        }

        $user = self::user();

        return $user ? Employee::findByUserId($user['id']) : null;
    }

    public static function logout(): void
    {
        self::$user = null;
        Session::destroy();
    }
}
