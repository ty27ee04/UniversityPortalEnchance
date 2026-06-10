# Project Update

This update documents the enhancement pass applied to the University Portal.

## What Was Implemented

1. MVC Refactoring
2. Enrollment Module
3. Role-Based Access Control Middleware
4. Automated Email Notifications via PHPMailer
5. Database Soft Deletes
6. Input Validation & Sanitization Engine

7. Shared View Helpers for student and admin page chrome

## How It Was Done

### 1) MVC Refactoring

The project was moved toward a lightweight MVC-style structure without changing it into a full framework app.

- `app/Core/Database.php` now owns the database connection.
- `app/Controllers/` contains request logic for authentication, enrollment, and admin actions.
- `app/Repositories/` contains the SQL/data access layer.
- `app/Support/` contains reusable helpers for auth, validation, and mail.
- `app/Support/Page.php` and `app/Support/Form.php` reduce repeated HTML scaffolding and form boilerplate.
- `app/bootstrap.php` loads Composer and the application classes.

The existing PHP pages still render HTML, but they now delegate business logic and much of the repeated page chrome to shared classes instead of handling SQL and validation inline.

### 2) Enrollment Module

A new [enrollment.php](enrollment.php) page was added for student enrollment requests.

- The page is restricted to logged-in student users.
- It captures course, intake period, study mode, and a notes field.
- It stores the request in the database through the shared repository layer.
- It triggers confirmation email notifications after successful submission.

### 3) Role-Based Access Control Middleware

The shared middleware in `app/Middleware/RoleMiddleware.php` checks the current session role before allowing access.

- Student pages such as `index.php`, `about.php`, `course.php`, `Sports.php`, and `contact.php` now require a student session.
- `admin.php` now requires an admin session.
- If a user is not allowed, they are redirected to the appropriate login page.

### 4) Automated Email Notifications via PHPMailer

Composer was added with `phpmailer/phpmailer` as a dependency.

- `app/Support/MailerService.php` sends outgoing email.
- Registration sends a welcome email to the student.
- Contact form submissions can notify the admin mailbox.
- Enrollment submissions send confirmation to the student and a notification to the admin mailbox.

The service can use SMTP when environment variables are provided, or fall back to the local mail transport.

### 5) Database Soft Deletes

The schema was updated so records are not hard-deleted when admin actions run.

- `users.is_deleted` is used when a student record is removed.
- `contact.is_deleted` is used when a contact message is removed.
- `contact.is_hidden` is used when a message is hidden from the dashboard.
- `enrollments.is_deleted` was added for enrollment records.

Admin actions now update these flags instead of deleting rows outright.

### 6) Input Validation & Sanitization Engine

`app/Support/Validation.php` centralizes input handling.

- It trims and sanitizes text.
- It validates email addresses.
- It enforces minimum lengths and matching fields.
- It converts validation errors into safe HTML output.

This helper is now used by the student login, registration, admin registration/login, and enrollment flows.

### 7) Shared View Helpers

To make the HTML less legacy, the repeated document shell and navigation/footer blocks were centralized into `app/Support/Page.php`.

- Student pages now use one shared header/footer renderer.
- Admin pages reuse shared admin header/footer helpers.
- Enrollment uses a small form helper for escaped values and selected options.

This reduces duplicated markup and makes future template changes much cheaper.

## How It Works

### Student Flow

1. The user registers through `registration.php`.
2. The controller validates the form, checks for duplicate emails, hashes the password, and stores the student.
3. PHPMailer sends a welcome message after successful registration.
4. The user logs in through `login.php`.
5. The session stores the user role as `student`.
6. Protected pages check that role through the middleware.
7. The new enrollment page lets the student submit an enrollment request.
8. The request is saved with soft-delete support and email confirmation is sent.

The student pages now render through shared view helpers instead of repeating the same page chrome in each file.

### Admin Flow

1. Admin login and registration go through shared controller logic.
2. The session stores the role as `admin`.
3. `admin.php` checks the role before showing dashboard data.
4. Contact and user actions are executed through the admin controller.
5. Deletions become soft deletes instead of permanent removals.

The admin screens also use shared page helpers for the common layout shell, which keeps the dashboard and auth screens consistent.

## Database Changes

The following schema expectations are now part of the project:

- `users`: soft delete and disable flags already existed and are now used by the controller layer.
- `contact`: hidden/deleted flags are used by the admin dashboard.
- `enrollments`: new table added for enrollment submissions.

## README Status

Yes, [README.md](README.md) was updated to include the new `enrollment.php` page, the `app/` structure, and setup notes for Composer and the database import.

## Notes

- The app is now organized around shared controllers, repositories, middleware, and services.
- It is still a PHP-first project, so the refactor is a lightweight MVC structure rather than a full framework migration.
- The feature set is implemented, but some existing pages still use legacy HTML layout code and can be migrated further if you want a full cleanup pass.
- The biggest remaining next step would be extracting the inline page-specific CSS into dedicated view components or CSS partials.