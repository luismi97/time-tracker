<?php

use App\Core\Csrf;
use App\Core\Session;

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function config(string $key, mixed $default = null): mixed
{
    static $cache = [];

    [$file, $rest] = array_pad(explode('.', $key, 2), 2, null);

    if (!array_key_exists($file, $cache)) {
        $path = BASE_PATH . "/config/{$file}.php";
        $cache[$file] = file_exists($path) ? require $path : [];
    }

    if ($rest === null) {
        return $cache[$file] ?: $default;
    }

    return $cache[$file][$rest] ?? $default;
}

/** Nombre del sitio configurado por el administrador (con respaldo al valor de config/app.php). */
function site_name(): string
{
    static $name = null;

    if ($name === null) {
        try {
            $settings = \App\Models\Settings::get();
            $name = $settings['app_name'] !== '' ? $settings['app_name'] : config('app.name');
        } catch (\Throwable $e) {
            $name = config('app.name');
        }
    }

    return $name;
}

/** Ruta publica del logo configurado (null si no se ha subido uno). */
function site_logo(): ?string
{
    static $logo = null;
    static $loaded = false;

    if (!$loaded) {
        $loaded = true;
        try {
            $logo = \App\Models\Settings::get()['logo_path'] ?: null;
        } catch (\Throwable $e) {
            $logo = null;
        }
    }

    return $logo;
}

function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function old_input(): array
{
    static $old = null;
    if ($old === null) {
        $old = Session::getFlash('old', []);
    }

    return $old;
}

function old(string $key, string $default = ''): string
{
    return e(old_input()[$key] ?? $default);
}

/** Estado de un checkbox tras un reenvio fallido de formulario (los checkboxes no marcados no llegan por POST). */
function old_checked(string $key, bool $default = false): bool
{
    $old = old_input();
    return $old ? !empty($old[$key]) : $default;
}

function form_errors(): array
{
    static $errors = null;
    if ($errors === null) {
        $errors = Session::getFlash('errors', []);
    }

    return $errors;
}

function field_error(string $field): ?string
{
    return form_errors()[$field] ?? null;
}

function flash(string $type, mixed $message): void
{
    Session::flash($type, $message);
}

/** Enlace de navegacion del sidebar, resaltado si coincide con la ruta actual. */
function nav_link(string $href, string $label): string
{
    $current = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/';
    $target = rtrim($href, '/') ?: '/';
    $isActive = $current === $target || str_starts_with($current, $target . '/');

    $classes = $isActive
        ? 'flex items-center gap-2 rounded-lg px-3 py-2 bg-indigo-600 text-white font-medium'
        : 'flex items-center gap-2 rounded-lg px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white transition-colors';

    return '<a href="' . e($href) . '" class="' . $classes . '">' . e($label) . '</a>';
}

/** Renderiza una vista dentro de un layout (por defecto layouts/app) y la envia al navegador. */
function view(string $name, array $data = []): void
{
    $layout = $data['layout'] ?? 'layouts/app';
    unset($data['layout']);

    extract($data);

    ob_start();
    require VIEWS_PATH . '/' . $name . '.php';
    $content = ob_get_clean();

    require VIEWS_PATH . '/' . $layout . '.php';
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(Csrf::token()) . '">';
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e($method) . '">';
}

function pagination_url(array $params, int $page): string
{
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

function format_hours(?float $hours): string
{
    return number_format((float) $hours, 2) . ' h';
}

function format_money(?float $amount): string
{
    return '$' . number_format((float) $amount, 2);
}

function format_date(?string $date): string
{
    return $date ? (new DateTimeImmutable($date))->format('d/m/Y') : '-';
}

function format_time(?string $datetime): string
{
    return $datetime ? (new DateTimeImmutable($datetime))->format('h:i A') : '-';
}
