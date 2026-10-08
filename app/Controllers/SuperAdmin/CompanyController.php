<?php

namespace App\Controllers\SuperAdmin;

use App\Core\Auth;
use App\Core\Validator;
use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;

class CompanyController
{
    public function index(): void
    {
        view('super/companies/index', [
            'title' => 'Empresas',
            'companies' => Company::allWithStats(),
        ]);
    }

    public function create(): void
    {
        view('super/companies/create', ['title' => 'Nueva empresa']);
    }

    public function store(): void
    {
        $data = $this->collectInput();
        $data['admin_email'] = trim($_POST['admin_email'] ?? '');
        $data['admin_password'] = $_POST['admin_password'] ?? '';

        $errors = $this->validate($data);
        $errors += Validator::validate($data, [
            'admin_email' => 'required|email|max:150',
            'admin_password' => 'required|min:8',
        ]);

        if (empty($errors['admin_email']) && User::emailExists($data['admin_email'])) {
            $errors['admin_email'] = 'Ese correo ya esta en uso.';
        }

        if ($errors) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect('/super/companies/create');
        }

        (new CompanyService())->create($data);
        flash('success', 'Empresa creada correctamente.');
        redirect('/super/companies');
    }

    public function edit(string $id): void
    {
        $company = $this->findOrFail($id);

        view('super/companies/edit', [
            'title' => 'Editar empresa',
            'company' => $company,
            'admins' => User::adminsForCompany((int) $company['id']),
        ]);
    }

    public function update(string $id): void
    {
        $company = $this->findOrFail($id);
        $data = $this->collectInput();
        $errors = $this->validate($data, (int) $company['id']);

        if ($errors) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect("/super/companies/{$company['id']}/edit");
        }

        Company::update((int) $company['id'], $data['name'], $data['slug'], $data['status']);
        flash('success', 'Empresa actualizada correctamente.');
        redirect("/super/companies/{$company['id']}/edit");
    }

    public function destroy(string $id): void
    {
        $company = $this->findOrFail($id);

        if (Auth::companyId() === (int) $company['id']) {
            Auth::leaveCompany();
        }

        (new CompanyService())->delete((int) $company['id']);
        flash('success', 'Empresa "' . $company['name'] . '" eliminada con todos sus datos.');
        redirect('/super/companies');
    }

    /** Entra a gestionar la empresa con el panel de administracion normal. */
    public function enter(string $id): void
    {
        $company = $this->findOrFail($id);
        Auth::enterCompany((int) $company['id']);
        redirect('/admin/dashboard');
    }

    public function exit(): void
    {
        Auth::leaveCompany();
        redirect('/super/companies');
    }

    private function findOrFail(string $id): array
    {
        $company = Company::find((int) $id);
        if (!$company) {
            flash('error', 'Empresa no encontrada.');
            redirect('/super/companies');
        }

        return $company;
    }

    private function collectInput(): array
    {
        $name = trim($_POST['name'] ?? '');
        $slug = slugify(trim($_POST['slug'] ?? '') !== '' ? $_POST['slug'] : $name);

        return [
            'name' => $name,
            'slug' => $slug,
            'status' => $_POST['status'] ?? 'active',
        ];
    }

    private function validate(array $data, ?int $excludeId = null): array
    {
        $errors = Validator::validate($data, [
            'name' => 'required|max:100',
            'slug' => 'required|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        if (empty($errors['slug']) && Company::slugExists($data['slug'], $excludeId)) {
            $errors['slug'] = 'Ese codigo de empresa ya esta en uso.';
        }

        return $errors;
    }
}
