<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

\App\Middleware\RoleMiddleware::requireStudent('login.php');

$controller = new \App\Controllers\EnrollmentController($conn);
$flashMessage = '';
$old = [
    'full_name' => $_SESSION['auth']['name'] ?? '',
    'email' => $_SESSION['auth']['email'] ?? '',
    'course_name' => '',
    'intake_period' => '',
    'study_mode' => '',
    'notes' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->submit($_POST);
    $flashMessage = $result['message'];

    if ($result['success']) {
        $old['course_name'] = '';
        $old['intake_period'] = '';
        $old['study_mode'] = '';
        $old['notes'] = '';
    } else {
        $old['full_name'] = \App\Support\Validation::sanitizeString($_POST['full_name'] ?? '');
        $old['email'] = \App\Support\Validation::sanitizeEmail($_POST['email'] ?? '');
        $old['course_name'] = \App\Support\Validation::sanitizeString($_POST['course_name'] ?? '');
        $old['intake_period'] = \App\Support\Validation::sanitizeString($_POST['intake_period'] ?? '');
        $old['study_mode'] = \App\Support\Validation::sanitizeString($_POST['study_mode'] ?? '');
        $old['notes'] = \App\Support\Validation::sanitizeString($_POST['notes'] ?? '');
    }
}
?>
<?php \App\Support\Page::renderHead('Enrollment | World\'s Biggest University'); ?>
    <style>
        .enrollment-shell {
            width: min(1100px, 92%);
            margin: -90px auto 80px;
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 24px;
        }

        .enrollment-card {
            background: #fff;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .12);
        }

        .enrollment-form input,
        .enrollment-form select,
        .enrollment-form textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d7ddea;
            border-radius: 10px;
            margin-bottom: 14px;
            font: inherit;
            background: #fff;
        }

        .enrollment-form textarea {
            min-height: 140px;
            resize: vertical;
        }

        .enrollment-submit {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 14px 18px;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .message-box {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 10px;
            font-weight: 600;
        }

        .message-success {
            background: #e8fff1;
            color: #166534;
        }

        .message-error {
            background: #fff1f2;
            color: #991b1b;
        }

        .enrollment-note {
            background: #eef4ff;
            border-left: 4px solid var(--primary);
            padding: 16px;
            border-radius: 12px;
            margin-top: 18px;
        }

        @media (max-width: 900px) {
            .enrollment-shell {
                grid-template-columns: 1fr;
            }
        }
    </style>

<?php \App\Support\Page::renderStudentHeader('enrollment.php', 'Enrollment Module', 'Submit a course request and receive automatic confirmation.', 'min-height:55vh;'); ?>

    <section class="enrollment-shell">
        <div class="enrollment-card">
            <h2>Student Enrollment</h2>

            <?php if ($flashMessage !== ''): ?>
                <div class="message-box <?php echo strpos($flashMessage, 'alert-success') !== false ? 'message-success' : 'message-error'; ?>">
                    <?php echo $flashMessage; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="enrollment-form" novalidate>
                <input type="text" name="full_name" placeholder="Full Name" value="<?php echo \App\Support\Form::value($old, 'full_name'); ?>" required>
                <input type="email" name="email" placeholder="Email Address" value="<?php echo \App\Support\Form::value($old, 'email'); ?>" required>
                <input type="text" name="course_name" placeholder="Program / Course Name" value="<?php echo \App\Support\Form::value($old, 'course_name'); ?>" required>

                <select name="intake_period" required>
                    <option value="">Select Intake Period</option>
                    <option value="Spring" <?php echo \App\Support\Form::checked($old['intake_period'], 'Spring'); ?>>Spring</option>
                    <option value="Summer" <?php echo \App\Support\Form::checked($old['intake_period'], 'Summer'); ?>>Summer</option>
                    <option value="Fall" <?php echo \App\Support\Form::checked($old['intake_period'], 'Fall'); ?>>Fall</option>
                </select>

                <select name="study_mode" required>
                    <option value="">Select Study Mode</option>
                    <option value="Full-Time" <?php echo \App\Support\Form::checked($old['study_mode'], 'Full-Time'); ?>>Full-Time</option>
                    <option value="Part-Time" <?php echo \App\Support\Form::checked($old['study_mode'], 'Part-Time'); ?>>Part-Time</option>
                    <option value="Online" <?php echo \App\Support\Form::checked($old['study_mode'], 'Online'); ?>>Online</option>
                </select>

                <textarea name="notes" placeholder="Why do you want to enroll?" required><?php echo \App\Support\Form::value($old, 'notes'); ?></textarea>

                <button type="submit" class="enrollment-submit">Submit Enrollment</button>
            </form>
        </div>

        <aside class="enrollment-card">
            <h2>What Happens Next</h2>
            <div class="enrollment-note">
                <p>Your request is stored with soft-delete support so admins can manage records safely.</p>
            </div>
            <div class="enrollment-note">
                <p>Automated email notifications are sent via PHPMailer after a successful submission.</p>
            </div>
            <div class="enrollment-note">
                <p>Validation and sanitization run before anything is written to the database.</p>
            </div>
        </aside>
    </section>

<?php \App\Support\Page::renderFooter(); ?>
