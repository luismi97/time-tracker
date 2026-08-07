<?php

namespace App\Controllers\Admin;

use App\Core\Paginator;
use App\Core\Validator;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmployeeService;

class EmployeeController
{
    public function index(): void
    {
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? '',
        ];

        $page = Paginator::currentPageFromRequest();
        $perPage = 10;
        $total = Employee::count($filters);
        $paginator = new Paginator($total, $perPage, $page);

        view('admin/employees/index', [
            'title' => 'Empleados',
            'employees' => Employee::paginate($filters, $perPage, $paginator->offset()),
            'paginator' => $paginator,
            'filters' => $filters,
        ]);
    }

    public function create(): void
    {
        view('admin/employees/create', ['title' => 'Nuevo empleado']);
    }

    public function store(): void
    {
        $data = $this->collectInput();
        $errors = $this->validate($data, true);

        if ($errors) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect('/admin/employees/create');
        }

        (new EmployeeService())->create($data);
        flash('success', 'Empleado creado correctamente.');
        redirect('/admin/employees');
    }

    public function edit(string $id): void
    {
        $employee = Employee::find((int) $id);
        if (!$employee) {
            flash('error', 'Empleado no encontrado.');
            redirect('/admin/employees');
        }

        view('admin/employees/edit', ['title' => 'Editar empleado', 'employee' => $employee]);
    }

    public function update(string $id): void
    {
        $employee = Employee::find((int) $id);
        if (!$employee) {
            flash('error', 'Empleado no encontrado.');
            redirect('/admin/employees');
        }

        $data = $this->collectInput();
        $errors = $this->validate($data, false, (int) $employee['user_id']);

        if ($errors) {
            flash('errors', $errors);
            flash('old', $_POST);
            redirect("/admin/employees/{$id}/edit");
        }

        (new EmployeeService())->update((int) $id, $data);
        flash('success', 'Empleado actualizado correctamente.');
        redirect('/admin/employees');
    }

    public function destroy(string $id): void
    {
        Employee::delete((int) $id);
        flash('success', 'Empleado eliminado correctamente.');
        redirect('/admin/employees');
    }

    public function toggleStatus(string $id): void
    {
        $employee = Employee::find((int) $id);
        if ($employee) {
            Employee::setStatus((int) $id, $employee['status'] === 'active' ? 'inactive' : 'active');
            flash('success', 'Estado del empleado actualizado.');
        }

        redirect('/admin/employees');
    }

    private function collectInput(): array
    {
        return [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'document_id' => trim($_POST['document_id'] ?? ''),
            'hire_date' => $_POST['hire_date'] ?? '',
            'hourly_rate' => $_POST['hourly_rate'] ?? '',
            'overtime_paid' => isset($_POST['overtime_paid']) ? 1 : 0,
            'has_lunch_break' => isset($_POST['has_lunch_break']) ? 1 : 0,
            'status' => $_POST['status'] ?? 'active',
            'password' => $_POST['password'] ?? '',
        ];
    }

    private function validate(array $data, bool $requirePassword, ?int $excludeUserId = null): array
    {
        $rules = [
            'full_name' => 'required|max:150',
            'email' => 'required|email|max:150',
            'hire_date' => 'required|date',
            'hourly_rate' => 'required|numeric|min_value:0',
            'status' => 'required|in:active,inactive',
        ];

        if ($requirePassword) {
            $rules['password'] = 'required|min:8';
        } elseif (!empty($data['password'])) {
            $rules['password'] = 'min:8';
        }

        $errors = Validator::validate($data, $rules);

        if (empty($errors['email']) && User::emailExists($data['email'], $excludeUserId)) {
            $errors['email'] = 'Ese correo ya esta en uso.';
        }

        return $errors;
    }
}
