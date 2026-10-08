<?php

namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        // Cierra la sesion si la cuenta o su empresa fueron desactivadas o eliminadas.
        $user = Auth::user();
        // Tambien si la sesion no corresponde a la empresa del usuario (p. ej. sesiones previas a multiempresa).
        $companyMismatch = !Auth::isSuperAdmin() && (int) ($user['company_id'] ?? 0) !== Auth::companyId();
        if (!$user || !Auth::isAllowed($user) || $companyMismatch) {
            Auth::logout();
            redirect('/login');
        }

        // Un empleado que sale de la red autorizada pierde la sesion en la siguiente peticion.
        if (!Auth::networkAllowed($user)) {
            Auth::logoutWithMessage(Auth::NETWORK_DENIED_MESSAGE);
        }
    }
}
