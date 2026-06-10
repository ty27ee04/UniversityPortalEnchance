# User Guide

This guide explains how to access and use the University Portal after the enhancement pass.

## Student Access

1. Open `http://localhost/university_portal/registration.php` to create a student account.
2. Use your email and password to log in at `http://localhost/university_portal/login.php`.
3. After login, you can access the student pages:
   - Home page: `index.php`
   - About page: `about.php`
   - Courses page: `course.php`
   - Sports page: `Sports.php`
   - Contact page: `contact.php`
   - Enrollment page: `enrollment.php`
4. Use `logout.php` when you are finished.

## Admin Access

1. Open `http://localhost/university_portal/admin_login.php`.
2. Log in using a valid admin ID and password.
3. After login, you can access `admin.php` to manage users and contact messages.
4. Use `admin_logout.php` to sign out.

## What Students Can Do

- Register and log in securely
- Browse university information
- Submit contact messages
- Submit enrollment requests
- Receive confirmation emails for supported actions

## What Admins Can Do

- Review contact messages
- Hide or unhide messages
- Disable or re-enable student accounts
- Soft delete records without permanently removing them

## Notes

- Student pages are protected by role-based access control.
- Admin pages are protected separately from student pages.
- If a page redirects you to login, verify that you are signed in with the correct role.
