<?php

namespace App\Core;

use App\Models\Employee;
use App\Models\Settings;
use App\Models\User;
use App\Support\IpAccess;

class Auth
{
    public const NETWORK_DENIED_MESSAGE = 'Solo puedes iniciar sesion desde la red autorizada de tu empresa.';

    private static ?array $user = null;

    /** Usuario si las credenciales son validas y la cuenta puede entrar (sin iniciar sesion). */
    public static function validateCredentials(string $email, string $password): ?array
    {
        $user = User::findByEmail($email);

        if (!$user || !self::isAllowed($user) || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        return $user;
    }

    public static function login(array $user): void
    {
        Session::regenerate();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['company_id'] = $user['company_id'];
        User::touchLastLogin($user['id']);
        self::$user = null;
    }

    /** Cuenta activa y, salvo para el super admin, perteneciente a una empresa activa. */
    public static function isAllowed(array $user): bool
    {
        if (!$user['is_active']) {
            return false;
        }

        return $user['role_name'] === 'super_admin' || $user['company_status'] === 'active';
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

    /** Empresa de la sesion: la del usuario, o la que el super admin eligio gestionar. */
    public static function companyId(): ?int
    {
        return isset($_SESSION['company_id']) ? (int) $_SESSION['company_id'] : null;
    }

    public static function isSuperAdmin(): bool
    {
        return self::role() === 'super_admin';
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isEmployee(): bool
    {
        return self::role() === 'employee';
    }

    /** Puede usar el panel /admin: administrador de empresa, o super admin dentro de una empresa. */
    public static function canManageCompany(): bool
    {
        return self::isAdmin() || (self::isSuperAdmin() && Tenant::check());
    }

    public static function enterCompany(int $companyId): void
    {
        if (self::isSuperAdmin()) {
            $_SESSION['company_id'] = $companyId;
        }
    }

    public static function leaveCompany(): void
    {
        if (self::isSuperAdmin()) {
            unset($_SESSION['company_id']);
        }
    }

    /** Pagina de inicio segun el rol del usuario autenticado. */
    public static function homePath(): string
    {
        if (self::isSuperAdmin()) {
            return self::companyId() !== null ? '/admin/dashboard' : '/super/companies';
        }

        return self::isAdmin() ? '/admin/dashboard' : '/employee/dashboard';
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

    /**
     * Restriccion de red: los empleados solo pueden entrar desde la red autorizada de su
     * empresa. Administradores y super admin pueden entrar desde cualquier lugar.
     */
    public static function networkAllowed(array $user): bool
    {
        if ($user['role_name'] !== 'employee') {
            return true;
        }

        return IpAccess::clientAllowed(Settings::allowedIpsFor((int) $user['company_id']));
    }

    public static function logout(): void
    {
        self::$user = null;
        Session::destroy();
    }

    /** Cierra la sesion y muestra un mensaje en el login (en una sesion nueva y limpia). */
    public static function logoutWithMessage(string $message): never
    {
        self::logout();
        Session::start();
        Session::regenerate();
        flash('error', $message);
        redirect('/login');
    }
}
