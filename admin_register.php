<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

if (\App\Middleware\RoleMiddleware::currentRole() === 'admin') {
    header('Location: admin.php');
    exit();
}

$controller = new \App\Controllers\AuthController($conn);
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->registerAdmin($_POST);

    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}
?>
<?php \App\Support\Page::renderHead('Admin Registration | World\'s Biggest University'); ?>

    <!-- Inline Admin UI CSS -->
    <style>
        .admin-header {
            min-height: 35vh;
            background:
                linear-gradient(rgba(8, 23, 56, 0.85), rgba(8, 23, 56, 0.85)),
                url("assets/images/bs.jpg") center / cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }

        .admin-header h1 {
            font-size: 2.2rem;
            margin-bottom: 6px;
        }

        .admin-header p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .admin-register-box {
            max-width: 460px;
            margin: -80px auto 80px;
            background: #ffffff;
            padding: 34px;
            border-radius: 16px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.18);
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .admin-register-box h2 {
            text-align: center;
            margin-bottom: 22px;
            font-weight: 600;
        }

        .admin-register-box input {
            width: 100%;
            padding: 13px 14px;
            margin-bottom: 16px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s ease;
        }

        .admin-register-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 77, 162, 0.15);
        }

        .admin-register-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .admin-register-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .success-msg {
            background: #e6fffa;
            color: #065f46;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: center;
            font-weight: 500;
        }

        .error-msg {
            background: #ffe5e5;
            color: #b91c1c;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: center;
            font-weight: 500;
        }

        .admin-info {
            margin-top: 30px;
            font-size: 0.95rem;
            color: #555;
            line-height: 1.6;
        }

        .admin-info ul {
            padding-left: 20px;
            margin-top: 10px;
        }

        .admin-info a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
    </style>

<?php \App\Support\Page::renderAdminHeader('Admin Registration', 'Create a new administrator account'); ?>

    <!-- ===== REGISTRATION CARD ===== -->
    <div class="admin-register-box">
        <h2>Register Administrator</h2>

        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="admin_id" placeholder="Admin ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" class="admin-register-btn">Register Admin</button>
        </form>

        <!-- ===== LOGIN INFO ===== -->
        <div class="admin-info">
            <hr>

            <h3 style="text-align:center;">Admin Login Information</h3>

            <p>
                This registration page is intended only for authorized university administrators.
                After successful registration, you can log in using the same
                <strong>Admin ID</strong> and <strong>Password</strong>.
            </p>


    <?php \App\Support\Page::renderAdminFooter(); ?>
            </p>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <section class="footer">
        <h4>World's Biggest University</h4>

        <p>
            Empowering students through education, innovation, and excellence.
        </p>

        <p>
            © <?php echo date("Y"); ?> World's Biggest University. All Rights Reserved.
        </p>

        <p>
            Designed & Developed by <strong>Modasiya Jaydip</strong>
        </p>
    </section>

</body>

</html>