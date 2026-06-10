<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class AdminRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    public function findByAdminId(string $adminId): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM admin WHERE admin_id = ? LIMIT 1');
        $stmt->bind_param('s', $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc() ?: null;
        $stmt->close();

        return $admin;
    }

    public function adminIdExists(string $adminId): bool
    {
        $stmt = $this->conn->prepare('SELECT id FROM admin WHERE admin_id = ? LIMIT 1');
        $stmt->bind_param('s', $adminId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function createAdmin(string $adminId, string $passwordHash): bool
    {
        $stmt = $this->conn->prepare('INSERT INTO admin (admin_id, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $adminId, $passwordHash);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
}
