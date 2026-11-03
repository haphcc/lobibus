<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';
    public const FIELD = '_csrf_token';
    public const HEADER = 'HTTP_X_CSRF_TOKEN';

    public static function token(): string
    {
        $token = Session::get(self::SESSION_KEY);
        if (is_string($token) && $token !== '') {
            return $token;
        }

        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        return $token;
    }

    public static function field(): string
    {
        return '<input type="hidden" name="' . self::FIELD . '" value="' . e(self::token()) . '">';
    }

    public static function validateRequest(): bool
    {
        $expected = (string) Session::get(self::SESSION_KEY, '');
        $provided = self::providedToken();

        return $expected !== ''
            && $provided !== ''
            && hash_equals($expected, $provided);
    }

    public static function injectIntoForms(string $html): string
    {
        $field = self::field();

        return (string) preg_replace_callback(
            '/<form\b(?=[^>]*\bmethod\s*=\s*["\']?post["\']?)[^>]*>/i',
            static function (array $matches) use ($field): string {
                return $matches[0] . "\n" . $field;
            },
            $html
        );
    }

    private static function providedToken(): string
    {
        $header = (string) ($_SERVER[self::HEADER] ?? '');
        if ($header !== '') {
            return $header;
        }

        return (string) ($_POST[self::FIELD] ?? '');
    }
}
