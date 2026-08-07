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

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [LoginController::class, 'show'], ['guest']);
$router->post('/login', [LoginController::class, 'store'], ['guest']);
$router->post('/logout', [LoginController::class, 'destroy'], ['auth']);

// Kiosco: acceso publico para marcar entrada/salida con codigo de empleado
// (solo funcional si el administrador habilito este modo en Configuracion).
$router->get('/kiosk', [KioskController::class, 'show']);
$router->post('/kiosk/lookup', [KioskController::class, 'lookup']);
$router->post('/kiosk/clock-in', [KioskController::class, 'clockIn']);
$router->post('/kiosk/clock-out', [KioskController::class, 'clockOut']);

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

    $router->get('/reports', [ReportController::class, 'create']);
    $router->post('/reports/preview', [ReportController::class, 'preview']);
    $router->post('/reports/generate', [ReportController::class, 'generate']);

    $router->get('/settings', [SettingsController::class, 'index']);
    $router->post('/settings/general', [SettingsController::class, 'updateGeneral']);
    $router->post('/settings/attendance-mode', [SettingsController::class, 'updateAttendanceMode']);
    $router->post('/settings/business-hours', [SettingsController::class, 'updateBusinessHours']);
});

$router->group(['prefix' => 'employee', 'middleware' => ['auth', 'employee']], function ($router) {
    $router->get('/dashboard', [EmployeeDashboardController::class, 'index']);

    $router->get('/attendance', [EmployeeAttendanceController::class, 'index']);
    $router->post('/attendance/clock-in', [EmployeeAttendanceController::class, 'clockIn']);
    $router->post('/attendance/clock-out', [EmployeeAttendanceController::class, 'clockOut']);

    $router->get('/profile', [ProfileController::class, 'index']);
});
