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

/** Nombre de la empresa activa (o el nombre de la app en el login y el panel del super admin). */
function site_name(): string
{
    $company = \App\Core\Tenant::company();

    return $company && $company['name'] !== '' ? $company['name'] : config('app.name');
}

/** Ruta publica del logo de la empresa activa (null si no hay empresa o no se ha subido uno). */
function site_logo(): ?string
{
    if (!\App\Core\Tenant::check()) {
        return null;
    }

    try {
        return \App\Models\Settings::get()['logo_path'] ?: null;
    } catch (\Throwable $e) {
        return null;
    }
}

/** Convierte un texto en un codigo apto para URL: "Cafe Lopez S.A." -> "cafe-lopez-s-a". */
function slugify(string $value): string
{
    $value = strtr(mb_strtolower($value, 'UTF-8'), [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
    ]);
    $value = trim((string) preg_replace('/[^a-z0-9]+/', '-', $value), '-');

    return rtrim(substr($value, 0, 50), '-');
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

/** Codigo de la moneda configurada por la empresa activa (CRC, USD o EUR). */
function currency_code(): string
{
    if (!\App\Core\Tenant::check()) {
        return 'USD';
    }

    $code = \App\Models\Settings::get()['currency'] ?? 'USD';
    return isset(\App\Models\Settings::CURRENCIES[$code]) ? $code : 'USD';
}

function currency_symbol(): string
{
    return \App\Models\Settings::CURRENCIES[currency_code()]['symbol'];
}

function format_money(?float $amount): string
{
    return currency_symbol() . number_format((float) $amount, 2);
}

function format_date(?string $date): string
{
    return $date ? (new DateTimeImmutable($date))->format('d/m/Y') : '-';
}

function format_time(?string $datetime): string
{
    return $datetime ? (new DateTimeImmutable($datetime))->format('h:i A') : '-';
}
