<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, array<string, callable|array{0: class-string, 1: string}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = $this->normalize($this->stripBasePath(parse_url($uri, PHP_URL_PATH) ?: '/'));
        if ($method === 'POST' && !Csrf::validateRequest()) {
            $this->rejectInvalidCsrf($path);
            return;
        }

        if ($this->isAdminPath($path) && !Auth::isAdmin()) {
            Session::flash('error', 'Bạn cần đăng nhập bằng tài khoản quản trị để vào trang quản trị.');
            header('Location: ' . \url('/login?redirect=' . rawurlencode($path)));
            return;
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 - Page not found';
            return;
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->{$action}();
            return;
        }

        $handler();
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }

    private function stripBasePath(string $path): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = str_replace('\\', '/', dirname($scriptName));
        if ($basePath === '/' || $basePath === '.') {
            return $path;
        }

        if (str_starts_with($path, $basePath)) {
            $stripped = substr($path, strlen($basePath));
            return $stripped === '' ? '/' : $stripped;
        }

        return $path;
    }

    private function isAdminPath(string $path): bool
    {
        return $path === '/admin' || str_starts_with($path, '/admin/');
    }

    private function rejectInvalidCsrf(string $path): void
    {
        http_response_code(419);

        if (str_starts_with($path, '/api/')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => 'CSRF token khong hop le hoac da het han.',
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return;
        }

        echo '419 - CSRF token khong hop le hoac da het han. Vui long tai lai trang va thu lai.';
    }
}
