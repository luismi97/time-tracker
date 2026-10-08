<?php

namespace App\Controllers;

use App\Services\MigrationService;

/**
 * Migraciones desde el navegador: /migrate?token=MIGRATE_TOKEN muestra las pendientes y un
 * boton para ejecutarlas. Sin MIGRATE_TOKEN en .env (o con un token incorrecto) responde 404.
 */
class MigrationController
{
    private const MIN_TOKEN_LENGTH = 16;

    public function show(): void
    {
        $token = $this->authorizeOrNotFound($_GET['token'] ?? '');

        view('migrations/index', [
            'title' => 'Migraciones',
            'layout' => 'layouts/guest',
            'token' => $token,
            'status' => (new MigrationService())->status(),
        ]);
    }

    public function run(): void
    {
        $token = $this->authorizeOrNotFound($_POST['token'] ?? '');
        $service = new MigrationService();
        $result = $service->runPending();

        view('migrations/index', [
            'title' => 'Migraciones',
            'layout' => 'layouts/guest',
            'token' => $token,
            'status' => $service->status(),
            'result' => $result,
        ]);
    }

    private function authorizeOrNotFound(string $token): string
    {
        $expected = (string) ($_ENV['MIGRATE_TOKEN'] ?? '');

        if (strlen($expected) < self::MIN_TOKEN_LENGTH || !hash_equals($expected, $token)) {
            http_response_code(404);
            view('errors/404', ['title' => 'Pagina no encontrada', 'layout' => 'layouts/error']);
            exit;
        }

        return $token;
    }
}
