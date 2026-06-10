<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

\App\Middleware\RoleMiddleware::requireStudent('login.php');
?>
<?php \App\Support\Page::renderHead('Sports | World\'s Biggest University'); ?>
<?php \App\Support\Page::renderStudentHeader('Sports.php', 'Sports & Athletics', 'Encouraging teamwork, discipline, and excellence through sports.', 'min-height: 60vh;'); ?>

    <section class="course">
        <h1>Our Sports Culture</h1>
        <p>
            We believe sports are an essential part of student development, promoting
            physical fitness, leadership, and team spirit.
        </p>

        <div class="row">
            <div class="course-col">
                <h3>Indoor Sports</h3>
                <p>
                    Chess, Table Tennis, Badminton, Carrom and other indoor games
                    to sharpen focus and strategy.
                </p>
            </div>

            <div class="course-col">
                <h3>Outdoor Sports</h3>
                <p>
                    Cricket, Football, Volleyball, Athletics and more to build
                    endurance and teamwork.
                </p>
            </div>

            <div class="course-col">
                <h3>Annual Sports Meet</h3>
                <p>
                    A grand sports festival featuring inter-department competitions,
                    awards, and celebrations.
                </p>
            </div>
        </div>
    </section>

    <section class="campus">
        <h1>Sports Grounds</h1>
        <p>World-class sports infrastructure across our campuses.</p>

        <div class="row">
            <div class="campus-col">
                <img src="assets/images/LONDON.jpg" alt="London Sports Ground">
                <div class="layer">
                    <h3>LONDON</h3>
                </div>
            </div>

            <div class="campus-col">
                <img src="assets/images/Tokyo.jpg" alt="Tokyo Sports Ground">
                <div class="layer">
                    <h3>TOKYO</h3>
                </div>
            </div>

            <div class="campus-col">
                <img src="assets/images/Delhi.jpg" alt="Delhi Sports Ground">
                <div class="layer">
                    <h3>DELHI</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="facilities">
        <h1>Sports Facilities</h1>
        <p>Modern infrastructure supporting professional training and recreation.</p>

        <div class="row">
            <div class="facilities-col">
                <img src="assets/images/play.jpg" alt="Playground">
                <h3>Largest Playground</h3>
                <p>
                    Spacious and well-maintained playgrounds for multiple sports
                    and athletic activities.
                </p>
            </div>

            <div class="facilities-col">
                <img src="assets/images/gym.jpg" alt="Gymnasium">
                <h3>Modern Gymnasium</h3>
                <p>
                    Fully equipped gym with professional trainers for fitness
                    and strength conditioning.
                </p>
            </div>

            <div class="facilities-col">
                <img src="assets/images/stadium.jpg" alt="Stadium">
                <h3>Sports Stadium</h3>
                <p>
                    Large stadium for hosting tournaments, matches, and
                    university-level competitions.
                </p>
            </div>
        </div>
    </section>

    <section class="cta">
        <h1>
            Train Hard, Play Fair,<br>
            Achieve Excellence
        </h1>
        <a href="contact.php" class="hero-btn">CONTACT SPORTS DEPARTMENT</a>
    </section>

<?php \App\Support\Page::renderFooter('World\'s Biggest University'); ?>
