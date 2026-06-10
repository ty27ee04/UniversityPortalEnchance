<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class ContactRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    public function createContact(array $data): bool
    {
        // 1. Prepare an OOSD parameterized MySQL statement to prevent SQL injection exploits
        $stmt = $this->conn->prepare(
            'INSERT INTO contact (name, email, message) VALUES (?, ?, ?)'
        );

        // 2. Bind parameters ('sss' means 3 string arguments)
        $stmt->bind_param(
            'sss',
            $data['name'],
            $data['email'],
            $data['message']
        );

        // 3. Execute query and store boolean results state
        $success = $stmt->execute();
        
        // 4. Always close statements to optimize system resources and prevent memory leaks
        $stmt->close();

        return $success;
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
