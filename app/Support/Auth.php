<?php

declare(strict_types=1);

namespace App\Support;

final class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['auth'] ?? null;
    }

    public static function loginStudent(array $user): void
    {
        $_SESSION['auth'] = [
            'role' => 'student',
            'id' => (int) $user['id'],
            'name' => $user['full_name'] ?? '',
            'email' => $user['email'] ?? '',
        ];
        $_SESSION['user'] = 'yes';
    }

    public static function loginAdmin(array $admin): void
    {
        $_SESSION['auth'] = [
            'role' => 'admin',
            'id' => (int) $admin['id'],
            'name' => $admin['admin_id'] ?? '',
            'email' => $admin['admin_id'] ?? '',
        ];
        $_SESSION['admin_id'] = $admin['admin_id'];
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['auth']);
    }

    public static function isRole(string $role): bool
    {
        return (self::user()['role'] ?? null) === $role;
    }

    public static function requireRole(string $role, string $redirect = 'login.php'): void
    {
        if (!self::isRole($role)) {
            header('Location: ' . $redirect);
            exit();
        }
    }

    public static function requireAnyRole(array $roles, string $redirect): void
    {
        $role = self::user()['role'] ?? null;
        if (!in_array($role, $roles, true)) {
            header('Location: ' . $redirect);
            exit();
        }
    }

    public static function logout(string $redirect = 'login.php'): void
    {
        session_unset();
        session_destroy();
        header('Location: ' . $redirect);
        exit();
    }
}
