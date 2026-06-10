<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

\App\Middleware\RoleMiddleware::requireStudent('login.php');
?>

<?php \App\Support\Page::renderHead('About Us | World\'s Biggest University'); ?>
<?php \App\Support\Page::renderStudentHeader('about.php', 'About Our University', 'Learn more about our journey, mission, and commitment to academic excellence.', 'min-height: 60vh;'); ?>

    <!-- ================= ABOUT CONTENT ================= -->
    <section class="course">
        <h1>Who We Are</h1>
        <p>
            A global institution shaping the future through education, research, and innovation.
        </p>

        <div class="row">
            <div class="course-col">
                <h3>Our History</h3>
                <p>
                    World's Biggest University is a renowned institution dedicated to providing
                    high-quality education to students across the globe. With a legacy spanning
                    decades, we have consistently delivered academic excellence and innovation.
                </p>
            </div>

            <div class="course-col">
                <h3>Our Mission</h3>
                <p>
                    Our mission is to empower individuals through education, research, and
                    innovation. We nurture critical thinking, creativity, and leadership to
                    prepare students for global challenges.
                </p>
            </div>

            <div class="course-col">
                <h3>Our Vision</h3>
                <p>
                    We envision a future where education transcends boundaries, enabling
                    students to become responsible global citizens and leaders in their
                    respective fields.
                </p>
            </div>
        </div>
    </section>

    <!-- ================= CTA ================= -->
    <section class="cta">
        <h1>
            Join a Community of Learners<br>
            Shaping the Future
        </h1>
        <a href="contact.php" class="hero-btn">CONTACT US</a>
    </section>

<?php \App\Support\Page::renderFooter('World\'s Biggest University'); ?>