<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

\App\Middleware\RoleMiddleware::requireStudent('login.php');
?>

<?php \App\Support\Page::renderHead('Courses | World\'s Biggest University'); ?>
<?php \App\Support\Page::renderStudentHeader('course.php', 'Our Academic Programs', 'Industry-focused courses designed to build skills, knowledge, and career readiness.', 'min-height: 60vh;'); ?>

    <!-- ================= COURSES INTRO ================= -->
    <section class="course">
        <h1>Courses We Offer</h1>
        <p>
            Our programs combine academic excellence with practical exposure,
            preparing students for global careers.
        </p>

        <div class="row">
            <div class="course-col">
                <h3>Undergraduate Programs</h3>
                <p>
                    Comprehensive bachelor’s programs focused on strong foundations,
                    hands-on learning, and industry exposure.
                </p>
            </div>

            <div class="course-col">
                <h3>Postgraduate Programs</h3>
                <p>
                    Advanced master’s programs emphasizing research, innovation,
                    and leadership development.
                </p>
            </div>

            <div class="course-col">
                <h3>Certification Courses</h3>
                <p>
                    Short-term professional courses designed to upgrade skills
                    and enhance employability.
                </p>
            </div>
        </div>
    </section>

    <!-- ================= COURSE CATALOG ================= -->
    <section class="facilities">
        <h1>Popular Courses</h1>
        <p>
            Explore some of our most in-demand academic and professional programs.
        </p>

        <div class="row">
            <div class="facilities-col">
                <img src="./assets/images/Web Development.jpg" alt="Web Development">
                <h3>Web Development</h3>
                <p>
                    Learn HTML, CSS, JavaScript, PHP, and modern frameworks
                    to build dynamic web applications.
                </p>
            </div>

            <div class="facilities-col">
                <img src="./assets/images/data science.jpg" alt="Data Science">
                <h3>Data Science</h3>
                <p>
                    Master data analysis, Python, machine learning, and
                    real-world data-driven problem solving.
                </p>
            </div>

            <div class="facilities-col">
                <img src="./assets/images/Mobile App Development.jpg" alt="Mobile App Development">
                <h3>Mobile App Development</h3>
                <p>
                    Build Android and iOS apps using Flutter, Kotlin,
                    Swift, and React Native.
                </p>
            </div>

            <div class="facilities-col">
                <img src="./assets/images/Digital Marketing Course.jpg" alt="Digital Marketing">
                <h3>Digital Marketing</h3>
                <p>
                    Learn SEO, social media marketing, content strategy,
                    and online brand building.
                </p>
            </div>

            <div class="facilities-col">
                <img src="./assets/images/Graphic Design.png" alt="Graphic Design">
                <h3>Graphic Design</h3>
                <p>
                    Develop creativity with design principles, typography,
                    and tools like Photoshop and Illustrator.
                </p>
            </div>

            <div class="facilities-col">
                <img src="./assets/images/Photography.jpeg" alt="Photography">
                <h3>Photography</h3>
                <p>
                    Learn composition, lighting techniques, camera handling,
                    and professional photo editing.
                </p>
            </div>
        </div>
    </section>

    <!-- ================= CTA ================= -->
    <section class="cta">
        <h1>
            Build Your Career With the Right Course<br>
            Start Learning Today
        </h1>
        <a href="contact.php" class="hero-btn">ENROLL NOW</a>
    </section>

<?php \App\Support\Page::renderFooter(); ?>