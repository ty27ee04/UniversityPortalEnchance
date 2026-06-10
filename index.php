<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

\App\Middleware\RoleMiddleware::requireStudent('login.php');
?>

<?php \App\Support\Page::renderHead('University website'); ?>
<?php \App\Support\Page::renderStudentHeader('index.php', "World's Biggest University", 'Making a website is now one of the easiest things in the world. You just need to learn HTML, CSS, and JavaScript.'); ?>

    <section class="course">
        <h1>Courses we offer</h1>
        <p>Explore our range of courses designed to help you succeed in your academic journey.</p>

        <div class="row">
            <div class="course-col">
                <h3>Intermediate</h3>
                <p>Prepare yourself with foundational knowledge and skills for advanced studies.</p>
            </div>

            <div class="course-col">
                <h3>Degree</h3>
                <p>Earn your degree in specialized fields with our comprehensive degree programs.</p>
            </div>
        </div>
    </section>

    <section class="campus">
        <h1>Our Global Campus</h1>
        <p>Experience our diverse and vibrant campuses across the globe.</p>

        <div class="row">
            <div class="campus-col">
                <img src="assets/images/LONDON.jpg" alt="London Campus">
                <div class="layer">
                    <h3>LONDON</h3>
                </div>
            </div>

            <div class="campus-col">
                <img src="assets/images/Tokyo.jpg" alt="Tokyo Campus">
                <div class="layer">
                    <h3>Tokyo</h3>
                </div>
            </div>

            <div class="campus-col">
                <img src="assets/images/Delhi.jpg" alt="Delhi Campus">
                <div class="layer">
                    <h3>Delhi</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="facilities">
        <h1>Our Facilities</h1>
        <p>Discover our top-notch facilities designed to support your academic and extracurricular needs.</p>

        <div class="row">
            <div class="facilities-col">
                <img src="assets/images/library.jpg" alt="Library">
                <h3>World Class Library</h3>
                <p>Access a vast collection of resources for your research and study needs.</p>
            </div>

            <div class="facilities-col">
                <img src="assets/images/play.jpg" alt="Playground">
                <h3>Largest Play Ground</h3>
                <p>Engage in recreational activities and sports in our expansive playground areas.</p>
            </div>

            <div class="facilities-col">
                <img src="assets/images/food.jpg" alt="Cafeteria">
                <h3>Tasty and Healthy Food</h3>
                <p>Enjoy nutritious and delicious meals prepared by our expert chefs.</p>
            </div>
        </div>
    </section>

    <section class="testimonials">
        <h1>What Our Students Say</h1>
        <p>Read testimonials from our satisfied students.</p>

        <div class="row">
            <div class="testimonial-col">
                <img src="assets/images/s1.jpg" alt="Student 1">
                <div>
                    <p>"Best University for Computer Engineering."</p>
                    <h3>Lila</h3>
                </div>
            </div>

            <div class="testimonial-col">
                <img src="assets/images/s2.jpg" alt="Student 2">
                <div>
                    <p>"Great learning environment and supportive faculty."</p>
                    <h3>Hettik Patel</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <h1>
            Enroll For Our Various Online Courses<br>
            Anywhere From The World
        </h1>
        <a href="contact.php" class="hero-btn">CONTACT US</a>
    </section>

<?php \App\Support\Page::renderFooter(); ?>