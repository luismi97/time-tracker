<?php

namespace App\Controllers;

use App\Core\Auth;

class HomeController
{
    public function index(): void
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        redirect(Auth::isAdmin() ? '/admin/dashboard' : '/employee/dashboard');
    }
}
