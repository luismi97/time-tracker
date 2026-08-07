<?php

namespace App\Controllers\Employee;

use App\Core\Auth;

class ProfileController
{
    public function index(): void
    {
        view('employee/profile', [
            'title' => 'Mi perfil',
            'employee' => Auth::employee(),
        ]);
    }
}
