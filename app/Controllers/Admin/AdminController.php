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

    protected function postFloat(string $key, float $default = 0): float
    {
        return (float) ($_POST[$key] ?? $default);
    }

    protected function queryInt(string $key, int $default = 0): int
    {
        return (int) ($_GET[$key] ?? $default);
    }

    protected function requireFields(array $fields): ?string
    {
        foreach ($fields as $field => $label) {
            if ($this->postString((string) $field) === '') {
                return "Vui lòng nhập {$label}.";
            }
        }

        return null;
    }

    protected function requireInteger(string $field, string $label, int $min = 1): ?string
    {
        $value = $this->postString($field);
        if (!preg_match('/^-?\d+$/', $value)) {
            return "{$label} phải là số nguyên.";
        }

        if ((int) $value < $min) {
            return "{$label} phải lớn hơn hoặc bằng {$min}.";
        }

        return null;
    }

    protected function requireNumber(string $field, string $label, float $min = 0): ?string
    {
        $value = $this->postString($field);
        if (!is_numeric($value)) {
            return "{$label} phải là số.";
        }

        if ((float) $value < $min) {
            return "{$label} phải lớn hơn hoặc bằng {$min}.";
        }

        return null;
    }

    protected function requireOptionalNumber(string $field, string $label, ?float $min = null, ?float $max = null): ?string
    {
        $value = $this->postString($field);
        if ($value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return "{$label} phải là số.";
        }

        $number = (float) $value;
        if ($min !== null && $number < $min) {
            return "{$label} phải lớn hơn hoặc bằng {$min}.";
        }
        if ($max !== null && $number > $max) {
            return "{$label} phải nhỏ hơn hoặc bằng {$max}.";
        }

        return null;
    }

    protected function requireAllowed(string $field, string $label, array $allowed): ?string
    {
        if (!in_array($this->postString($field), $allowed, true)) {
            return "{$label} không hợp lệ.";
        }

        return null;
    }

    protected function requireDateTime(string $field, string $label): ?string
    {
        $value = $this->postString($field);
        $date = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $value);
        if (!$date || $date->format('Y-m-d\TH:i') !== $value) {
            return "{$label} không đúng định dạng.";
        }

        return null;
    }
}
