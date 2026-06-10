<?php

declare(strict_types=1);

namespace App\Support;

final class Validation
{
    public static function sanitizeString(?string $value): string
    {
        return trim((string) $value);
    }

    public static function sanitizeEmail(?string $value): string
    {
        return strtolower(trim((string) $value));
    }

    public static function required(array $data, array $fields): array
    {
        $errors = [];

        foreach ($fields as $field) {
            if (self::sanitizeString($data[$field] ?? '') === '') {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }

        return $errors;
    }

    public static function email(string $value, string $label = 'Email'): ?string
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : $label . ' must be valid.';
    }

    public static function minLength(string $value, int $length, string $label): ?string
    {
        return mb_strlen($value) >= $length ? null : sprintf('%s must be at least %d characters long.', $label, $length);
    }

    public static function matches(string $first, string $second, string $label = 'Values'): ?string
    {
        return $first === $second ? null : $label . ' do not match.';
    }

    public static function toHtml(array $errors): string
    {
        if (empty($errors)) {
            return '';
        }

        $items = array_map(static fn (string $error): string => '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>', $errors);

        return '<div class="form-errors"><div class="alert alert-danger"><ul>' . implode('', $items) . '</ul></div></div>';
    }
}
