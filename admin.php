<?php
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

// 2. Enforce your security middleware checkpoint
\App\Middleware\RoleMiddleware::requireAdmin('admin_login.php');

// 3. Instantiate the UserRepository using the global connection variable
$userRepository = new \App\Repositories\UserRepository($conn);

// 4. Handle incoming Admin Actions (Disable, Enable, Soft Delete)
if (isset($_GET['delete_user'])) {
    $userId = (int)$_GET['delete_user'];
    // Execute soft delete by changing the flag to 1 instead of running a destructive DELETE query
    $stmt = $conn->prepare('UPDATE users SET is_deleted = 1 WHERE id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
    
    header('Location: admin.php');
    exit();
}

if (isset($_GET['disable_user'])) {
    $userId = (int)$_GET['disable_user'];
    $stmt = $conn->prepare('UPDATE users SET is_disabled = 1 WHERE id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
    
    header('Location: admin.php');
    exit();
}

if (isset($_GET['enable_user'])) {
    $userId = (int)$_GET['enable_user'];
    $stmt = $conn->prepare('UPDATE users SET is_disabled = 0 WHERE id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
    
    header('Location: admin.php');
    exit();
}

// 5. Call the newly added method to fetch your active dataset array safely
$searchTerm = isset($_GET['u_search']) ? \App\Support\Validation::sanitizeString($_GET['u_search']) : null;

$activeUsersList = $userRepository->getActiveUsers($searchTerm);

/* ================= SESSION TIMEOUT ================= */
$timeout = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

$adminController = new \App\Controllers\AdminController($conn);

/* ================= CONTACT ACTIONS ================= */
if (isset($_GET['hide_contact'])) {
    $adminController->hideContact((int) $_GET['hide_contact']);
}
if (isset($_GET['unhide_contact'])) {
    $adminController->unhideContact((int) $_GET['unhide_contact']);
}
if (isset($_GET['delete_contact'])) {
    $adminController->deleteContact((int) $_GET['delete_contact']);
}

/* ================= CONTACT PAGINATION + SEARCH ================= */
$c_limit = 5;
$c_page = max(1, (int)($_GET['c_page'] ?? 1));
$c_start = ($c_page - 1) * $c_limit;
$c_search = trim($_GET['c_search'] ?? '');

$c_where = "is_deleted=0";
if ($c_search !== '') {
    $safe = $conn->real_escape_string($c_search);
    $c_where .= " AND (name LIKE '%$safe%' OR email LIKE '%$safe%')";
}

$contacts = $conn->query(
    "SELECT * FROM contact WHERE $c_where ORDER BY id DESC LIMIT $c_start,$c_limit"
);

$c_total = $conn->query(
    "SELECT COUNT(*) FROM contact WHERE $c_where"
)->fetch_row()[0];
$c_pages = ceil($c_total / $c_limit);

/* ================= USER PAGINATION + SEARCH ================= */
$u_limit = 5;
$u_page = max(1, (int)($_GET['u_page'] ?? 1));
$u_start = ($u_page - 1) * $u_limit;
$u_search = trim($_GET['u_search'] ?? '');

$u_where = "is_deleted=0";
?>
<?php \App\Support\Page::renderHead('Admin Dashboard'); ?>
    <style>
        .dashboard-header {
            min-height: 35vh;
            background: linear-gradient(rgba(8, 23, 56, .85), rgba(8, 23, 56, .85)), url("assets/images/bs.jpg") center/cover;
            color: #fff;
            text-align: center;
            padding-top: 60px
        }

        .header-actions {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 22px;
            background: #e74c3c;
            color: #fff;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .logout-btn:hover { background: #c0392b; }

        /* Premium Styled Academic Management Trigger */
        .matrix-mgmt-btn {
            display: inline-block;
            padding: 10px 24px;
            background: #2ecc71;
            color: #fff;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4);
            transition: all 0.3s ease;
        }
        .matrix-mgmt-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.6);
        }

        .dashboard {
            width: 90%;
            margin: -60px auto 80px;
        }

        .card {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, .15);
            margin-bottom: 40px;
        }

        .search-box {
            margin: 15px 0;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-box input {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            width: 250px;
        }

        .search-box button {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background: #0a4da2;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            font-size: 0.95rem;
            text-align: left;
        }

        th {
            background: #f4f6fb;
            color: #333;
            font-weight: bold;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 14px;
            background: #f1f1f1;
            border-radius: 8px;
            margin: 0 4px;
            font-weight: 600;
            text-decoration: none;
            color: #333;
        }

        .pagination a.active {
            background: #0a4da2;
            color: #fff;
        }

        .action-btn {
            padding: 6px 14px;
            border-radius: 6px;
            color: #fff;
            font-size: .85rem;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }

        .hide { background: #f39c12 }
        .unhide { background: #27ae60 }
        .delete { background: #c0392b }
        .disable { background: #e67e22 }
        .enable { background: #2ecc71 }
    </style>

    <section class="dashboard-header">
        <h1>Admin Dashboard Workspace</h1>
        <p>Authenticated Session Identity: <strong><?= htmlspecialchars($_SESSION['admin_id']) ?></strong></p>
        
        <div class="header-actions">
            <a href="admin_academic.php" class="matrix-mgmt-btn">⚙️ Manage Academic Offerings Matrix</a>
            <a href="admin_logout.php" class="logout-btn">Logout Workspace</a>
        </div>
    </section>

    <div class="dashboard">

        <div class="card">
            <h2>Contact Messages Log Index</h2>

            <form method="get" class="search-box">
                <input type="text" name="c_search" value="<?= htmlspecialchars($c_search) ?>" placeholder="Search by name or keyword...">
                <button type="submit">Search</button>
                <?php if (!empty($_GET['c_search'])): ?>
                    <a href="admin.php" class="action-btn" style="background:#6c757d; text-decoration:none; padding:8px 14px; border-radius:8px; color:white;">Clear</a>
                <?php endif; ?>
            </form>

            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message Inquiry Payload</th>
                    <th>Status Flag</th>
                    <th>Operational Access Checks</th>
                </tr>
                <?php while ($c = $contacts->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['message']) ?></td>
                        <td><span style="padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:bold; background:<?= $c['is_hidden'] ? '#fef9e7;color:#f39c12;' : '#e8f8f5;color:#27ae60;' ?>"><?= $c['is_hidden'] ? 'Hidden' : 'Visible' ?></span></td>
                        <td>
                            <?= !$c['is_hidden'] ? "<a class='action-btn hide' href='?hide_contact={$c['id']}'>Hide</a>" : "<a class='action-btn unhide' href='?unhide_contact={$c['id']}'>Unhide</a>" ?>
                            <a class="action-btn delete" href="?delete_contact=<?= $c['id'] ?>" onclick="return confirm('CRITICAL CONFIRMATION REQUIRED:\n\nAre you sure you want to soft-delete this contact message inquiry transaction?\nThis hides records from the workspace index but preserves relational tables histories.');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <div class="pagination">
                <?php for ($i = 1; $i <= $c_pages; $i++): ?>
                    <a class="<?= $i == $c_page ? 'active' : '' ?>" href="?c_page=<?= $i ?>&c_search=<?= urlencode($c_search) ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>

        <div class="card">
            <h2>Registered Students Account Directory</h2>

            <form method="get" class="search-box">
                <input type="text" name="u_search" value="<?= htmlspecialchars($u_search) ?>" placeholder="Search student by identity matches...">
                <button type="submit">Search</button>
                <?php if (!empty($_GET['u_search'])): ?>
                    <a href="admin.php" class="action-btn" style="background:#6c757d; text-decoration:none; padding:8px 14px; border-radius:8px; color:white;">Clear</a>
                <?php endif; ?>
            </form>

            <table>
                <tr>
                    <th>Student Full Name</th>
                    <th>Email Address Identity</th>
                    <th>Account Access Status</th>
                    <th>Operational Control Flags</th>
                </tr>
                <?php if (!empty($activeUsersList)): ?>
                    <?php foreach ($activeUsersList as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars((string)$u['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)$u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span style="padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:bold; background:<?= (int)($u['is_disabled'] ?? 0) === 1 ? '#fadbd8;color:#c0392b;' : '#e8f8f5;color:#27ae60;' ?>"><?= (int)($u['is_disabled'] ?? 0) === 1 ? 'Disabled' : 'Active' ?></span></td>
                            <td>
                                <?php if ((int)($u['is_disabled'] ?? 0) !== 1): ?>
                                    <a class='action-btn disable' href='?disable_user=<?= $u['id'] ?>'>Disable</a>
                                <?php else: ?>
                                    <a class='action-btn enable' href='?enable_user=<?= $u['id'] ?>'>Enable</a>
                                <?php endif; ?>
                                
                                <a class="action-btn delete" href="?delete_user=<?= $u['id'] ?>" onclick="return confirm('CRITICAL CONFIRMATION REQUIRED:\n\nAre you sure you want to execute a soft-delete operation on this student record profile?\nAll historical database entries will be preserved for administrative integrity auditing.');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 25px; color:#7f8c8d; font-weight:bold;">No matching active student profiles located within current directory indexing bounds.</td>
                    </tr>
                <?php endif; ?>
            </table>

            <div class="pagination">
                <?php for ($i = 1; $i <= $u_page; $i++): ?>
                    <a class="<?= $i == $u_page ? 'active' : '' ?>" href="?u_page=<?= $i ?>&u_search=<?= urlencode($u_search) ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>

    </div>

<?php \App\Support\Page::renderFooter('World\'s Biggest University'); ?>