<?php
declare(strict_types=1);

// 1. Initialize bootstrap (handles autoloading and sets up global $conn)
require_once __DIR__ . '/app/bootstrap.php';

// 2. Enforce security access middleware checkpoint
\App\Middleware\RoleMiddleware::requireStudent('login.php');

$message = '';

// 3. Handle unified Repository form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Instantiate your decoupled ContactRepository using the global connection variable
    $contactRepo = new \App\Repositories\ContactRepository($conn);
    
    // Extract and clean the incoming $_POST data fields
    $name    = \App\Support\Validation::sanitizeString($_POST['name'] ?? '');
    $email   = \App\Support\Validation::sanitizeEmail($_POST['email'] ?? '');
    $msgText = \App\Support\Validation::sanitizeString($_POST['message'] ?? '');

    // Validate that required fields are not empty
    $errors = \App\Support\Validation::required(
        compact('name', 'email', 'msgText'), 
        ['name', 'email', 'msgText']
    );

    if (empty($errors)) {
        // Save to database via your clean Repository pattern method
        $success = $contactRepo->createContact([
            'name'    => $name,
            'email'   => $email,
            'message' => $msgText
        ]);

        if ($success) {
            $message = "<div class='alert alert-success' style='color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 12px; margin-bottom: 20px; border-radius: 8px; font-weight: 600;'>Message sent successfully! Our team will get back to you soon.</div>";
        } else {
            $message = "<div class='alert alert-danger' style='color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 12px; margin-bottom: 20px; border-radius: 8px; font-weight: 600;'>Failed to submit your message. Please try again.</div>";
        }
    } else {
        // Convert input validation engine error arrays into clean HTML items list warnings
        $message = \App\Support\Validation::toHtml($errors);
    }
}
?>
<?php \App\Support\Page::renderHead('Contact Us | World\'s Biggest University'); ?>

    <style>
        /* force form to be clickable */
        .contact-form-wrapper,
        .contact-form-wrapper * {
            position: relative !important;
            z-index: 50 !important;
            pointer-events: auto !important;
        }

        .contact-form-wrapper {
            background: #ffffff;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
        }

        .contact-form-wrapper h3 {
            margin-bottom: 16px;
        }

        .contact-form-wrapper input,
        .contact-form-wrapper textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 14px;
            font-size: 0.95rem;
            font-family: inherit;
        }

        .contact-form-wrapper textarea {
            height: 130px;
            resize: none;
        }

        .contact-submit-btn {
            width: 100%;
            padding: 14px;
            background: #0a4da2;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }

        .contact-submit-btn:hover {
            background: #083b7a;
        }

        /* contact info card */
        .contact-info-box {
            background: #ffffff;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
            text-align: left;
        }

        .info-item {
            display: flex;
            gap: 14px;
            margin-bottom: 18px;
        }

        .info-item span {
            font-size: 1.4rem;
        }
    </style>

<?php \App\Support\Page::renderStudentHeader('contact.php', 'Contact Us', 'We’re here to help you', 'min-height:60vh;'); ?>

    <section class="course">
        <h1>Get in Touch</h1>
        <p>Send us a message and we’ll respond shortly.</p>

        <div class="row" style="margin-top: 5%;">
            <div class="course-col contact-form-wrapper">
                <h3>Send a Message</h3>

                <?= $message ?>

                <form method="post" action="contact.php">
                    <input type="text" name="name" placeholder="Your Name" value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="email" name="email" placeholder="Your Email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <textarea name="message" placeholder="Your Message" required><?= htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <button type="submit" class="contact-submit-btn">Send Message</button>
                </form>
            </div>

            <div class="course-col contact-info-box">
                <h3>Contact Information</h3>

                <div class="info-item">
                    <span>📍</span>
                    <p><strong>Address</strong><br>Ahmedabad, India</p>
                </div>

                <div class="info-item">
                    <span>📞</span>
                    <p><strong>Phone</strong><br>+91 98765 43210</p>
                </div>

                <div class="info-item">
                    <span>📧</span>
                    <p><strong>Email</strong><br>info@wbu.edu</p>
                </div>

                <div class="info-item">
                    <span>🕒</span>
                    <p><strong>Hours</strong><br>Mon–Fri, 9AM–5PM</p>
                </div>
            </div>
        </div>
    </section>

<?php \App\Support\Page::renderFooter(); ?>