<?php

namespace App\Middleware;

use App\Core\Auth;

class GuestMiddleware
{
    public static function handle(): void
    {
        if (Auth::check()) {
            redirect(Auth::isAdmin() ? '/admin/dashboard' : '/employee/dashboard');
        }
    }
}
