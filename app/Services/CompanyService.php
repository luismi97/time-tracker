<?php

namespace App\Services;

use App\Core\Database;
use App\Models\BusinessHours;
use App\Models\Company;
use App\Models\Role;
use App\Models\Settings;
use App\Models\User;

class CompanyService
{
    /** Crea la empresa con su configuracion, horario por defecto y su primer administrador. */
    public function create(array $data): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $companyId = Company::create($data['name'], $data['slug'], $data['status']);
            Settings::createFor($companyId);
            BusinessHours::createFor($companyId);
            User::create($data['admin_email'], $data['admin_password'], Role::ADMIN, $companyId);
            $pdo->commit();
            return $companyId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** Elimina la empresa con todos sus datos (en cascada) y su logo subido. */
    public function delete(int $companyId): void
    {
        $logoPath = Settings::logoPathFor($companyId);
        Company::delete($companyId);

        if ($logoPath && is_file(BASE_PATH . '/public' . $logoPath)) {
            @unlink(BASE_PATH . '/public' . $logoPath);
        }
    }
}
