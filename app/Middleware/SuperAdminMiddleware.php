<?php

namespace App\Middleware;

use App\Core\Auth;

class SuperAdminMiddleware
{
    public static function handle(): void
    {
        if (!Auth::isSuperAdmin()) {
            http_response_code(403);
            view('errors/403', ['title' => 'Acceso denegado', 'layout' => 'layouts/error']);
            exit;
        }
    }
}
