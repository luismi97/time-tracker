<?php

/** @var \App\Core\Router $router */

use App\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\EmployeeController;
use App\Controllers\Admin\ReportController;
use App\Controllers\Admin\SettingsController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Controllers\Employee\ProfileController;
use App\Controllers\HomeController;
use App\Controllers\KioskController;
use App\Controllers\MigrationController;
use App\Controllers\SuperAdmin\CompanyAdminController;
use App\Controllers\SuperAdmin\CompanyController;

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [LoginController::class, 'show'], ['guest']);
$router->post('/login', [LoginController::class, 'store'], ['guest']);
$router->post('/logout', [LoginController::class, 'destroy'], ['auth']);

// Migraciones desde el navegador, protegidas por MIGRATE_TOKEN del .env (404 si no esta configurado).
$router->get('/migrate', [MigrationController::class, 'show']);
$router->post('/migrate', [MigrationController::class, 'run']);

// Kiosco: acceso publico por empresa para marcar entrada/salida con codigo de empleado
// (solo funcional si el administrador de la empresa habilito este modo en Configuracion).
$router->get('/kiosk', [KioskController::class, 'select']);
$router->get('/kiosk/{slug}', [KioskController::class, 'show']);
$router->post('/kiosk/{slug}/lookup', [KioskController::class, 'lookup']);
$router->post('/kiosk/{slug}/clock-in', [KioskController::class, 'clockIn']);
$router->post('/kiosk/{slug}/clock-out', [KioskController::class, 'clockOut']);

// Super admin: gestion de empresas independientes y de sus administradores.
$router->group(['prefix' => 'super', 'middleware' => ['auth', 'super_admin']], function ($router) {
    $router->get('/companies', [CompanyController::class, 'index']);
    $router->get('/companies/create', [CompanyController::class, 'create']);
    $router->post('/companies', [CompanyController::class, 'store']);
    $router->get('/companies/{id}/edit', [CompanyController::class, 'edit']);
    $router->post('/companies/{id}', [CompanyController::class, 'update']);
    $router->post('/companies/{id}/delete', [CompanyController::class, 'destroy']);
    $router->post('/companies/{id}/enter', [CompanyController::class, 'enter']);
    $router->post('/exit', [CompanyController::class, 'exit']);

    $router->post('/companies/{id}/admins', [CompanyAdminController::class, 'store']);
    $router->post('/companies/{id}/admins/{userId}/toggle-status', [CompanyAdminController::class, 'toggleStatus']);
    $router->post('/companies/{id}/admins/{userId}/password', [CompanyAdminController::class, 'updatePassword']);
    $router->post('/companies/{id}/admins/{userId}/delete', [CompanyAdminController::class, 'destroy']);
});

$router->group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function ($router) {
    $router->get('/dashboard', [AdminDashboardController::class, 'index']);

    $router->get('/employees', [EmployeeController::class, 'index']);
    $router->get('/employees/create', [EmployeeController::class, 'create']);
    $router->post('/employees', [EmployeeController::class, 'store']);
    $router->get('/employees/{id}/edit', [EmployeeController::class, 'edit']);
    $router->post('/employees/{id}', [EmployeeController::class, 'update']);
    $router->post('/employees/{id}/delete', [EmployeeController::class, 'destroy']);
    $router->post('/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus']);

    $router->get('/attendance', [AdminAttendanceController::class, 'index']);
    $router->get('/attendance/{id}/edit', [AdminAttendanceController::class, 'edit']);
    $router->post('/attendance/{id}', [AdminAttendanceController::class, 'update']);

    $router->get('/reports', [ReportController::class, 'create']);
    $router->post('/reports/preview', [ReportController::class, 'preview']);
    $router->post('/reports/generate', [ReportController::class, 'generate']);

    $router->get('/settings', [SettingsController::class, 'index']);
    $router->post('/settings/general', [SettingsController::class, 'updateGeneral']);
    $router->post('/settings/attendance-mode', [SettingsController::class, 'updateAttendanceMode']);
    $router->post('/settings/business-hours', [SettingsController::class, 'updateBusinessHours']);
    $router->post('/settings/network', [SettingsController::class, 'updateNetwork']);
});

$router->group(['prefix' => 'employee', 'middleware' => ['auth', 'employee']], function ($router) {
    $router->get('/dashboard', [EmployeeDashboardController::class, 'index']);

    $router->get('/attendance', [EmployeeAttendanceController::class, 'index']);
    $router->post('/attendance/clock-in', [EmployeeAttendanceController::class, 'clockIn']);
    $router->post('/attendance/clock-out', [EmployeeAttendanceController::class, 'clockOut']);
    $router->get('/attendance/{id}/edit', [EmployeeAttendanceController::class, 'edit']);
    $router->post('/attendance/{id}', [EmployeeAttendanceController::class, 'update']);

    $router->get('/profile', [ProfileController::class, 'index']);
});
