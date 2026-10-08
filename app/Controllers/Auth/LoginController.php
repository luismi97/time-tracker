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

        $user = Auth::validateCredentials($email, $password);
        if (!$user) {
            flash('error', 'Credenciales invalidas o cuenta inactiva.');
            redirect('/login');
        }

        if (!Auth::networkAllowed($user)) {
            flash('error', Auth::NETWORK_DENIED_MESSAGE);
            redirect('/login');
        }

        Auth::login($user);

        redirect(Auth::homePath());
    }

    public function destroy(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
