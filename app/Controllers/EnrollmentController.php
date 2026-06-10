<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Support\Auth;
use App\Support\MailerService;
use App\Support\Validation;
use mysqli;

final class EnrollmentController
{
    private UserRepository $users;
    private MailerService $mailer;

    public function __construct(private mysqli $conn)
    {
        $this->users = new UserRepository($conn);
        $this->mailer = new MailerService();
    }

    public function submit(array $input): array
    {
        $fullName = Validation::sanitizeString($input['full_name'] ?? '');
        $email = Validation::sanitizeEmail($input['email'] ?? '');
        $courseName = Validation::sanitizeString($input['course_name'] ?? '');
        $intakePeriod = Validation::sanitizeString($input['intake_period'] ?? '');
        $studyMode = Validation::sanitizeString($input['study_mode'] ?? '');
        $notes = Validation::sanitizeString($input['notes'] ?? '');

        $errors = [];
        $errors = array_merge($errors, Validation::required(compact('fullName', 'email', 'courseName', 'intakePeriod', 'studyMode'), ['fullName', 'email', 'courseName', 'intakePeriod', 'studyMode']));

        if ($email !== '' && ($error = Validation::email($email, 'Email address'))) {
            $errors[] = $error;
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => Validation::toHtml($errors), 'errors' => $errors];
        }

        $student = null;
        if (Auth::isRole('student')) {
            $student = $this->users->getActiveStudentById((int) (Auth::user()['id'] ?? 0));
        }

        $created = $this->users->createEnrollment([
            'full_name' => $fullName,
            'email' => $email,
            'course_name' => $courseName,
            'intake_period' => $intakePeriod,
            'study_mode' => $studyMode,
            'notes' => $notes,
            'user_id' => (int) ($student['id'] ?? 0),
        ]);

        if ($created) {
            $this->mailer->sendEnrollmentConfirmation($fullName, $email, $courseName);

            return ['success' => true, 'message' => "<div class='alert alert-success'>Enrollment submitted successfully. Check your email for confirmation.</div>"];
        }

        return ['success' => false, 'message' => "<div class='alert alert-danger'>Unable to submit enrollment. Please try again.</div>"];
    }
}
