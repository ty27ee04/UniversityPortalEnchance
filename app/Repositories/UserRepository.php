<?php

declare(strict_types=1);

namespace App\Repositories;

use mysqli;

final class UserRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->conn->prepare('SELECT id FROM users WHERE email = ? AND is_deleted = 0 LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function createStudent(string $fullName, string $email, string $passwordHash): bool
    {
        $stmt = $this->conn->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $fullName, $email, $passwordHash);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function findStudentByEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE email = ? AND is_deleted = 0 LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc() ?: null;
        $stmt->close();

        return $user;
    }

    public function createEnrollment(array $data): bool
    {
        $stmt = $this->conn->prepare('INSERT INTO enrollments (full_name, email, course_name, intake_period, study_mode, notes, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param(
            'ssssssi',
            $data['full_name'],
            $data['email'],
            $data['course_name'],
            $data['intake_period'],
            $data['study_mode'],
            $data['notes'],
            $data['user_id']
        );
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function getActiveStudentById(int $id): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE id = ? AND is_deleted = 0 AND is_disabled = 0 LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc() ?: null;
        $stmt->close();

        return $user;
    }
}
