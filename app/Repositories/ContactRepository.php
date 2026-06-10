<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class ContactRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    public function create(string $name, string $email, string $message): bool
    {
        $stmt = $this->conn->prepare('INSERT INTO contact (name, email, message) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $message);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function setHidden(int $id, bool $hidden): bool
    {
        $flag = $hidden ? 1 : 0;
        $stmt = $this->conn->prepare('UPDATE contact SET is_hidden = ? WHERE id = ?');
        $stmt->bind_param('ii', $flag, $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->conn->prepare('UPDATE contact SET is_deleted = 1 WHERE id = ?');
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
}
