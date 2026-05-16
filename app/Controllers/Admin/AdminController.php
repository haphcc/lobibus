<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

abstract class AdminController extends Controller
{
    protected function redirect(string $path, string $type = '', string $message = ''): void
    {
        $query = [];
        if ($type !== '' && $message !== '') {
            $query[$type] = $message;
        }

        $target = url($path);
        if ($query !== []) {
            $target .= '?' . http_build_query($query);
        }

        header('Location: ' . $target);
        exit;
    }

    protected function postString(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $default));
    }

    protected function postInt(string $key, int $default = 0): int
    {
        return (int) ($_POST[$key] ?? $default);
    }

    protected function queryInt(string $key, int $default = 0): int
    {
        return (int) ($_GET[$key] ?? $default);
    }

    protected function requireFields(array $fields): ?string
    {
        foreach ($fields as $field => $label) {
            if ($this->postString((string) $field) === '') {
                return "{$label} is required.";
            }
        }

        return null;
    }
}
