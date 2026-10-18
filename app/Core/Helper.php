<?php

declare(strict_types=1);

if (!function_exists('config')) {
    function config(string $file): array
    {
        $path = dirname(__DIR__, 2) . '/config/' . $file . '.php';
        return is_file($path) ? require $path : [];
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $configuredBase = trim(config('app')['base_url'] ?? '');
        $base = $configuredBase !== '' ? rtrim($configuredBase, '/') : base_path();
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('base_path')) {
    function base_path(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $base = str_replace('\\', '/', dirname($scriptName));
        return ($base === '/' || $base === '.') ? '' : rtrim($base, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
