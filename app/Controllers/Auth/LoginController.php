<?php

namespace App\Controllers\Auth;

use App\Core\Auth;

class LoginController
{
    public function show(): void
    {
        view('auth/login', ['title' => 'Iniciar sesion', 'layout' => 'layouts/guest']);
    }

    public function store(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            flash('error', 'Debes ingresar correo y contrasena.');
            redirect('/login');
        }

        if (!Auth::attempt($email, $password)) {
            flash('error', 'Credenciales invalidas o cuenta inactiva.');
            redirect('/login');
        }

        redirect(Auth::isAdmin() ? '/admin/dashboard' : '/employee/dashboard');
    }

    public function destroy(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
