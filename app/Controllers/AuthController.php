<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Support\Auth;
use App\Support\MailerService;
use App\Support\Validation;
use mysqli;

final class AuthController
{
    private UserRepository $users;
    private AdminRepository $admins;
    private MailerService $mailer;

    public function __construct(private mysqli $conn)
    {
        $this->users = new UserRepository($conn);
        $this->admins = new AdminRepository($conn);
        $this->mailer = new MailerService();
    }

    public function registerStudent(array $input): array
    {
        $fullName = Validation::sanitizeString($input['fullname'] ?? '');
        $email = Validation::sanitizeEmail($input['email'] ?? '');
        $password = (string) ($input['password'] ?? '');
        $repeatPassword = (string) ($input['repeat_password'] ?? '');

        $errors = [];
        $errors = array_merge($errors, Validation::required(compact('fullName', 'email', 'password', 'repeatPassword'), ['fullName', 'email', 'password', 'repeatPassword']));

        if ($email !== '' && ($error = Validation::email($email, 'Email address'))) {
            $errors[] = $error;
        }
        if ($password !== '' && ($error = Validation::minLength($password, 8, 'Password'))) {
            $errors[] = $error;
        }
        if ($password !== '' && $repeatPassword !== '' && ($error = Validation::matches($password, $repeatPassword, 'Passwords'))) {
            $errors[] = $error;
        }
        if ($email !== '' && $this->users->emailExists($email)) {
            $errors[] = 'This email is already registered.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => Validation::toHtml($errors), 'errors' => $errors];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $created = $this->users->createStudent($fullName, $email, $passwordHash);

        if ($created) {
            $this->mailer->sendRegistrationConfirmation($fullName, $email);

            return ['success' => true, 'message' => "<div class='alert alert-success'>Registration successful. You can now log in.</div>"];
        }

        return ['success' => false, 'message' => "<div class='alert alert-danger'>Something went wrong. Please try again.</div>"];
    }

    public function loginStudent(array $input): array
    {
        $email = Validation::sanitizeEmail($input['email'] ?? '');
        $password = (string) ($input['password'] ?? '');

        $errors = [];
        if ($email === '' || $password === '') {
            $errors[] = 'All fields are required.';
        } elseif (($error = Validation::email($email, 'Email address'))) {
            $errors[] = $error;
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(' ', $errors)];
        }

        $user = $this->users->findStudentByEmail($email);
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found.'];
        }

        if ((int) ($user['is_disabled'] ?? 0) === 1) {
            return ['success' => false, 'message' => 'This account is disabled. Please contact support.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid password.'];
        }

        Auth::loginStudent($user);

        return ['success' => true, 'message' => 'Logged in successfully.'];
    }

    public function loginAdmin(array $input): array
    {
        $adminId = Validation::sanitizeString($input['admin_id'] ?? '');
        $password = (string) ($input['password'] ?? '');

        if ($adminId === '' || $password === '') {
            return ['success' => false, 'message' => 'All fields are required.'];
        }

        $admin = $this->admins->findByAdminId($adminId);
        if (!$admin || !password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid Admin ID or Password.'];
        }

        Auth::loginAdmin($admin);

        return ['success' => true, 'message' => 'Logged in successfully.'];
    }

    public function registerAdmin(array $input): array
    {
        $adminId = Validation::sanitizeString($input['admin_id'] ?? '');
        $password = (string) ($input['password'] ?? '');
        $confirm = (string) ($input['confirm_password'] ?? '');

        $errors = [];
        if ($adminId === '' || $password === '' || $confirm === '') {
            $errors[] = 'All fields are required.';
        }
        if ($password !== '' && ($error = Validation::minLength($password, 6, 'Password'))) {
            $errors[] = $error;
        }
        if ($password !== '' && $confirm !== '' && ($error = Validation::matches($password, $confirm, 'Passwords'))) {
            $errors[] = $error;
        }
        if ($adminId !== '' && $this->admins->adminIdExists($adminId)) {
            $errors[] = 'Admin ID already exists.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => Validation::toHtml($errors), 'errors' => $errors];
        }

        $created = $this->admins->createAdmin($adminId, password_hash($password, PASSWORD_DEFAULT));

        if ($created) {
            return ['success' => true, 'message' => '<div class="success-msg">Admin registered successfully. You can now log in.</div>'];
        }

        return ['success' => false, 'message' => '<div class="error-msg">Registration failed. Please try again.</div>'];
    }
}
