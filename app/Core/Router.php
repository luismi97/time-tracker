<?php

namespace App\Core;

use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\EmployeeMiddleware;
use App\Middleware\GuestMiddleware;
use Closure;

class Router
{
    private const MIDDLEWARE_MAP = [
        'auth' => AuthMiddleware::class,
        'guest' => GuestMiddleware::class,
        'admin' => AdminMiddleware::class,
        'employee' => EmployeeMiddleware::class,
    ];

    private array $routes = [];
    private array $groupPrefixes = [];
    private array $groupMiddleware = [];

    public function get(string $uri, array $action, array $middleware = []): void
    {
        $this->add('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, array $action, array $middleware = []): void
    {
        $this->add('POST', $uri, $action, $middleware);
    }

    public function group(array $attributes, Closure $callback): void
    {
        $this->groupPrefixes[] = trim($attributes['prefix'] ?? '', '/');
        $this->groupMiddleware[] = $attributes['middleware'] ?? [];

        $callback($this);

        array_pop($this->groupPrefixes);
        array_pop($this->groupMiddleware);
    }

    private function add(string $method, string $uri, array $action, array $middleware): void
    {
        $prefix = implode('/', array_filter($this->groupPrefixes));
        $fullUri = '/' . trim($prefix . '/' . trim($uri, '/'), '/');

        $mergedMiddleware = $middleware;
        foreach (array_reverse($this->groupMiddleware) as $groupMw) {
            $mergedMiddleware = array_merge($groupMw, $mergedMiddleware);
        }

        $this->routes[] = [
            'method' => $method,
            'uri' => $fullUri === '' ? '/' : $fullUri,
            'action' => $action,
            'middleware' => $mergedMiddleware,
        ];
    }

    public function dispatch(string $method, string $requestUri): void
    {
        $method = strtoupper($method);
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper((string) $_POST['_method']);
        }

        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $route['uri']);
            if (!preg_match('#^' . $pattern . '$#', $path, $matches)) {
                continue;
            }

            array_shift($matches);

            foreach ($route['middleware'] as $key) {
                $middlewareClass = self::MIDDLEWARE_MAP[$key] ?? null;
                if ($middlewareClass) {
                    $middlewareClass::handle();
                }
            }

            [$controllerClass, $methodName] = $route['action'];
            (new $controllerClass())->$methodName(...$matches);
            return;
        }

        http_response_code(404);
        view('errors/404', ['title' => 'Pagina no encontrada', 'layout' => 'layouts/error']);
    }
}
