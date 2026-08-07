<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;

class EmployeeService
{
    public function create(array $data): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $userId = User::create($data['email'], $data['password'], Role::EMPLOYEE);
            $employeeId = Employee::create($data, $userId);
            $pdo->commit();
            return $employeeId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function update(int $employeeId, array $data): void
    {
        $employee = Employee::find($employeeId);
        Employee::update($employeeId, $data);
        User::updateCredentials($employee['user_id'], $data['email'], $data['password'] ?: null);
    }
}
