<?php

declare(strict_types=1);

namespace App\Support;

final class Form
{
    public static function old(array $data, string $key, string $default = ''): string
    {
        return (string) ($data[$key] ?? $default);
    }

    public static function checked(string $current, string $expected): string
    {
        return $current === $expected ? 'selected' : '';
    }

    public static function value(array $data, string $key, string $default = ''): string
    {
        return htmlspecialchars(self::old($data, $key, $default), ENT_QUOTES, 'UTF-8');
    }
}
