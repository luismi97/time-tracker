<?php

namespace App\Middleware;

use App\Core\Auth;

class AdminMiddleware
{
    public static function handle(): void
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            view('errors/403', ['title' => 'Acceso denegado', 'layout' => 'layouts/error']);
            exit;
        }
    }
}
