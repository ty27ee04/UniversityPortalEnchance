<?php

declare(strict_types=1);

namespace App\Support;

final class Page
{
    public static function renderHead(string $title, string $bodyClass = ''): void
    {
        $bodyClassAttribute = $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') . '"' : '';

        echo '<!DOCTYPE html>' . PHP_EOL;
        echo '<html lang="en">' . PHP_EOL;
        echo '<head>' . PHP_EOL;
        echo '    <meta charset="UTF-8">' . PHP_EOL;
        echo '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' . PHP_EOL;
        echo '    <title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>' . PHP_EOL;
        echo '    <link rel="preconnect" href="https://fonts.googleapis.com">' . PHP_EOL;
        echo '    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . PHP_EOL;
        echo '    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;600;700&display=swap" rel="stylesheet">' . PHP_EOL;
        echo '    <link rel="stylesheet" href="assets/css/main.css">' . PHP_EOL;
        echo '</head>' . PHP_EOL;
        echo '<body' . $bodyClassAttribute . '>' . PHP_EOL;
    }

    public static function renderStudentHeader(string $activePage, string $headline, string $subtext, string $headerStyle = ''): void
    {
        $styleAttribute = $headerStyle !== '' ? ' style="' . htmlspecialchars($headerStyle, ENT_QUOTES, 'UTF-8') . '"' : '';

        echo '<section class="header"' . $styleAttribute . '>' . PHP_EOL;
        echo '    <nav>' . PHP_EOL;
        echo '        <a href="index.php"><img src="assets/images/logo.jpg" class="profile_img" alt="University Logo"></a>' . PHP_EOL;
        echo '        <div class="nav-links">' . PHP_EOL;
        echo '            <ul>' . PHP_EOL;

        $links = [
            'index.php' => 'HOME',
            'about.php' => 'ABOUT',
            'Sports.php' => 'SPORTS',
            'course.php' => 'COURSE',
            'enrollment.php' => 'ENROLLMENT',
            'contact.php' => 'CONTACT',
            'logout.php' => 'LOGOUT',
        ];

        foreach ($links as $href => $label) {
            $activeClass = self::matchesActive($activePage, $href) ? ' class="active"' : '';
            echo '                <li><a href="' . $href . '"' . $activeClass . '>' . $label . '</a></li>' . PHP_EOL;
        }

        echo '            </ul>' . PHP_EOL;
        echo '        </div>' . PHP_EOL;
        echo '    </nav>' . PHP_EOL;
        echo '    <div class="text-box">' . PHP_EOL;
        echo '        <h1>' . htmlspecialchars($headline, ENT_QUOTES, 'UTF-8') . '</h1>' . PHP_EOL;
        echo '        <p>' . htmlspecialchars($subtext, ENT_QUOTES, 'UTF-8') . '</p>' . PHP_EOL;
        echo '    </div>' . PHP_EOL;
        echo '</section>' . PHP_EOL;
    }

    public static function renderFooter(string $brand = "World's Biggest University", string $developer = 'Modasiya Jaydip'): void
    {
        echo '<section class="footer">' . PHP_EOL;
        echo '    <h4>' . htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') . '</h4>' . PHP_EOL;
        echo '    <p>Empowering students through education, innovation, and excellence. Building future leaders with knowledge, skills, and values.</p>' . PHP_EOL;
        echo '    <p>© ' . date('Y') . ' ' . htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') . '. All Rights Reserved.</p>' . PHP_EOL;
        echo '    <p>Designed &amp; Developed by <strong>' . htmlspecialchars($developer, ENT_QUOTES, 'UTF-8') . '</strong></p>' . PHP_EOL;
        echo '</section>' . PHP_EOL;
        echo '</body>' . PHP_EOL;
        echo '</html>' . PHP_EOL;
    }

    public static function renderAdminHeader(string $heading, string $subtext): void
    {
        echo '<section class="admin-header">' . PHP_EOL;
        echo '    <div>' . PHP_EOL;
        echo '        <h1>' . htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') . '</h1>' . PHP_EOL;
        echo '        <p>' . htmlspecialchars($subtext, ENT_QUOTES, 'UTF-8') . '</p>' . PHP_EOL;
        echo '    </div>' . PHP_EOL;
        echo '</section>' . PHP_EOL;
    }

    public static function renderAdminFooter(string $developer = 'Modasiya Jaydip'): void
    {
        echo '<section class="footer">' . PHP_EOL;
        echo '    <h4>World\'s Biggest University</h4>' . PHP_EOL;
        echo '    <p>© ' . date('Y') . ' World\'s Biggest University. All Rights Reserved.</p>' . PHP_EOL;
        echo '    <p>Designed &amp; Developed by <strong>' . htmlspecialchars($developer, ENT_QUOTES, 'UTF-8') . '</strong></p>' . PHP_EOL;
        echo '</section>' . PHP_EOL;
        echo '</body>' . PHP_EOL;
        echo '</html>' . PHP_EOL;
    }

    private static function matchesActive(string $activePage, string $href): bool
    {
        return strtolower($activePage) === strtolower($href);
    }
}
