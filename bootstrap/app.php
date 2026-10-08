<?php

define('BASE_PATH', dirname(__DIR__));
define('VIEWS_PATH', BASE_PATH . '/resources/views');

require BASE_PATH . '/vendor/autoload.php';

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Router;
use App\Core\Session;
use App\Core\Tenant;
use App\Models\Company;

if (file_exists(BASE_PATH . '/.env')) {
    Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();
}

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

Session::start();

// Empresa activa: la del usuario autenticado, o la que eligio el super admin.
// (El kiosco publico la define a partir del codigo de empresa en la URL.)
if (Auth::companyId() !== null && ($company = Company::find(Auth::companyId()))) {
    Tenant::set($company);
}

// Verificacion CSRF global para toda solicitud que modifique estado.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
}

$router = new Router();
require BASE_PATH . '/routes/web.php';
