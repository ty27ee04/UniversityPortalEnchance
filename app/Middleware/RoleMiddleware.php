<?php

declare(strict_types=1);

namespace App\Middleware;

final class RoleMiddleware
{
    public static function currentRole(): ?string
    {
        if (!empty($_SESSION['auth']['role'])) {
            return (string) $_SESSION['auth']['role'];
        }

        if (!empty($_SESSION['admin_id'])) {
            return 'admin';
        }

        if (!empty($_SESSION['user'])) {
            return 'student';
        }

        return null;
    }

    public static function requireRole(array|string $roles, string $redirect): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $allowedRoles = is_array($roles) ? $roles : [$roles];
        if (!in_array(self::currentRole(), $allowedRoles, true)) {
            header('Location: ' . $redirect);
            exit();
        }
    }

    public static function requireStudent(string $redirect = 'login.php'): void
    {
        self::requireRole('student', $redirect);
    }

    public static function requireAdmin(string $redirect = 'admin_login.php'): void
    {
        self::requireRole('admin', $redirect);
    }
}
