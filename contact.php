<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

\App\Middleware\RoleMiddleware::requireStudent('login.php');

\App\Support\Page::renderHead('Contact Us');
\App\Support\Page::renderStudentHeader('contact.php', 'Contact Us', 'Get in touch with us');
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->submitContact($_POST);

    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}
?>
<?php \App\Support\Page::renderHead('Contact Us | World\'s Biggest University'); ?>

    <!-- 🔴 INLINE FIXES (IMPORTANT) -->
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

    <!-- CONTACT -->
    <section class="course">
        <h1>Get in Touch</h1>
        <p>Send us a message and we’ll respond shortly.</p>

        <?php if ($success): ?>
            <p style="color:green;font-weight:600;"><?php echo $success; ?></p>
        <?php elseif ($error): ?>
            <p style="color:red;font-weight:600;"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="row">
            <!-- FORM -->
            <div class="course-col contact-form-wrapper">
                <h3>Send a Message</h3>

                <form method="post">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <textarea name="message" placeholder="Your Message" required></textarea>
                    <button type="submit" class="contact-submit-btn">Send Message</button>
                </form>
            </div>

            <!-- INFO -->
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