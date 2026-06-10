<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

if (\App\Middleware\RoleMiddleware::currentRole() !== null) {
    header('Location: index.php');
    exit();
}

$controller = new \App\Controllers\AuthController($conn);
$alerts = '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Portal | Registration</title>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/login-Registration.css">
</head>

<body>

    <div class="container">
        <h1>Student Registration</h1>
        <?php
        if (isset($_POST['submit'])) {
            $result = $controller->registerStudent($_POST);
            $alerts = $result['message'];
        }
        echo $alerts;
        ?>



        <form action="registration.php" method="post" novalidate>

            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input
                    type="text"
                    id="fullname"
                    name="fullname"
                    placeholder="Enter your full name"
                    required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your university email"
                    required
                    autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimum 8 characters"
                    required
                    autocomplete="new-password">
                <small class="hint">Password must be at least 8 characters.</small>
            </div>

            <div class="form-group">
                <label for="repeat_password">Confirm Password</label>
                <input
                    type="password"
                    id="repeat_password"
                    name="repeat_password"
                    placeholder="Re-enter your password"
                    required
                    autocomplete="new-password">
            </div>

            <div class="form-btn">
                <button type="submit" name="submit" class="btn">
                    Register
                </button>
            </div>

        </form>

        <p>
            Already registered?
            <a href="login.php">Login here</a>
        </p>

    </div>

</body>

</html>