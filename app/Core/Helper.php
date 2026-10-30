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

if (!function_exists('load_env')) {
    function load_env(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '' || getenv($key) !== false) {
                continue;
            }

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"'))
                || (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

load_env(dirname(__DIR__, 2) . '/.env');
