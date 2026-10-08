<?php

namespace App\Controllers\SuperAdmin;

use App\Core\Validator;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;

/** Administradores de cada empresa, gestionados por el super admin. */
class CompanyAdminController
{
    public function store(string $id): void
    {
        $company = $this->findCompanyOrFail($id);
        $data = [
            'admin_email' => trim($_POST['admin_email'] ?? ''),
            'admin_password' => $_POST['admin_password'] ?? '',
        ];

        $errors = Validator::validate($data, [
            'admin_email' => 'required|email|max:150',
            'admin_password' => 'required|min:8',
        ]);

        if (empty($errors['admin_email']) && User::emailExists($data['admin_email'])) {
            $errors['admin_email'] = 'Ese correo ya esta en uso.';
        }

        if ($errors) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect($this->editUrl($company));
        }

        User::create($data['admin_email'], $data['admin_password'], Role::ADMIN, (int) $company['id']);
        flash('success', 'Administrador agregado.');
        redirect($this->editUrl($company));
    }

    public function toggleStatus(string $id, string $userId): void
    {
        $company = $this->findCompanyOrFail($id);
        $admin = $this->findAdminOrFail($company, $userId);

        User::setActive((int) $admin['id'], !$admin['is_active']);
        flash('success', 'Estado del administrador actualizado.');
        redirect($this->editUrl($company));
    }

    public function updatePassword(string $id, string $userId): void
    {
        $company = $this->findCompanyOrFail($id);
        $admin = $this->findAdminOrFail($company, $userId);
        $password = $_POST['password'] ?? '';

        if (strlen($password) < 8) {
            flash('error', 'La nueva contrasena debe tener al menos 8 caracteres.');
            redirect($this->editUrl($company));
        }

        User::updatePassword((int) $admin['id'], $password);
        flash('success', 'Contrasena de ' . $admin['email'] . ' actualizada.');
        redirect($this->editUrl($company));
    }

    public function destroy(string $id, string $userId): void
    {
        $company = $this->findCompanyOrFail($id);
        $admin = $this->findAdminOrFail($company, $userId);

        User::delete((int) $admin['id']);
        flash('success', 'Administrador eliminado.');
        redirect($this->editUrl($company));
    }

    private function findCompanyOrFail(string $id): array
    {
        $company = Company::find((int) $id);
        if (!$company) {
            flash('error', 'Empresa no encontrada.');
            redirect('/super/companies');
        }

        return $company;
    }

    private function findAdminOrFail(array $company, string $userId): array
    {
        $admin = User::findAdminInCompany((int) $userId, (int) $company['id']);
        if (!$admin) {
            flash('error', 'Administrador no encontrado.');
            redirect($this->editUrl($company));
        }

        return $admin;
    }

    private function editUrl(array $company): string
    {
        return "/super/companies/{$company['id']}/edit";
    }
}
