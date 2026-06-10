# Validation Guide

Use this guide to verify the enhancement work in the University Portal.

## 1. Check Syntax

Run syntax checks on the edited files:

```bash
php -l index.php
php -l about.php
php -l course.php
php -l Sports.php
php -l contact.php
php -l enrollment.php
php -l login.php
php -l registration.php
php -l admin_login.php
php -l admin_register.php
php -l admin.php
php -l logout.php
php -l admin_logout.php
```

Also check the shared classes:

```bash
php -l app/bootstrap.php
php -l app/Core/Database.php
php -l app/Support/Page.php
php -l app/Support/Form.php
php -l app/Support/Auth.php
php -l app/Support/Validation.php
php -l app/Support/MailerService.php
php -l app/Middleware/RoleMiddleware.php
php -l app/Controllers/AuthController.php
php -l app/Controllers/AdminController.php
php -l app/Controllers/EnrollmentController.php
php -l app/Repositories/UserRepository.php
php -l app/Repositories/AdminRepository.php
php -l app/Repositories/ContactRepository.php
```

## 2. Verify Database Import

Import `database/university_portal.sql` into MySQL and confirm these tables exist:

- `users`
- `admin`
- `contact`
- `enrollments`

Confirm the soft-delete fields are present:

- `users.is_deleted`
- `users.is_disabled`
- `contact.is_deleted`
- `contact.is_hidden`
- `enrollments.is_deleted`

## 3. Verify Student Flow

1. Register a student account.
2. Confirm the registration succeeds and the account is stored in `users`.
3. Log in with the new student account.
4. Confirm protected pages open only after login.
5. Open `enrollment.php` and submit a sample enrollment request.
6. Confirm the enrollment data is stored and the page shows a success message.

## 4. Verify Admin Flow

1. Log in as an admin.
2. Open `admin.php`.
3. Confirm the dashboard loads both contact messages and user tables.
4. Use hide/unhide on a contact row and confirm the `is_hidden` value changes.
5. Disable a user and confirm `is_disabled` changes.
6. Delete a record and confirm it is soft-deleted instead of physically removed.

## 5. Verify RBAC

1. Open a student page while logged out and confirm it redirects to `login.php`.
2. Open `admin.php` while logged out and confirm it redirects to `admin_login.php`.
3. Log in as a student and confirm admin pages stay blocked.
4. Log in as an admin and confirm student-only routes stay blocked.

## 6. Verify Email Notifications

1. Register a student and confirm the welcome email path runs.
2. Submit a contact message and confirm the admin notification path runs.
3. Submit an enrollment request and confirm the student confirmation path runs.

If SMTP is not configured, PHPMailer can still be exercised locally with the default transport settings.

## 7. Verify View Refactor

Open the student pages and confirm the shared header/footer layout is consistent across:

- `index.php`
- `about.php`
- `course.php`
- `Sports.php`
- `contact.php`
- `enrollment.php`

Open the admin screens and confirm the shared helper-driven shell is used there as well.

## 8. Where the Changes Live

- MVC-style app layer: `app/`
- Portal access guide: `USER_GUIDE.md`
- Change log: `UPDATE.md`
- Validation checklist: `VALIDATION.md`
- Project summary and setup notes: `README.md`
