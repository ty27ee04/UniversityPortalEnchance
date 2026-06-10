<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

if (\App\Middleware\RoleMiddleware::currentRole() !== null) {
    header('Location: index.php');
    exit();
}

$controller = new \App\Controllers\AuthController($conn);
$error = '';

if (isset($_POST['login'])) {
    $result = $controller->loginStudent($_POST);

    if ($result['success']) {
        header('Location: index.php');
        exit();
    }

    $error = $result['message'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Portal | Student Login</title>

    <!-- Shared Login / Register CSS -->
    <link rel="stylesheet" href="./assets/css/login-Registration.css">
</head>

<body>

    <div class="container">

        

        <!-- PAGE TITLE -->
        <h1>Student Login</h1>

        <!-- ✅ ERROR BLOCK (TOP — SAME AS REGISTER PAGE) -->
        <?php if (!empty($error)): ?>
            <div class="form-errors">
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form action="login.php" method="post" novalidate>

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
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password">
            </div>

            <div class="form-btn">
                <button type="submit" name="login" class="btn">
                    Login
                </button>
            </div>

        </form>

        <p class="form-footer">
            Not registered yet?
            <a href="registration.php">Create an account</a>
        </p>

    </div>

</body>

</html>