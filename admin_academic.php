<?php
declare(strict_types=1);
require_once __DIR__ . '/app/bootstrap.php';
\App\Middleware\RoleMiddleware::requireAdmin('admin_login.php');

$msg = '';

if (isset($_GET['get_subjects_by_course'])) {
    header('Content-Type: application/json');
    $cId = (int)$_GET['get_subjects_by_course'];
    $stmt = $conn->prepare("SELECT id, name FROM academic_subjects WHERE course_id = ? AND is_hidden = 0");
    $stmt->bind_param('i', $cId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    echo json_encode($res);
    exit();
}

if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['target_id'];
    $type = $_GET['toggle_status'];

    match($type) {
        'delete_course'   => $conn->query("UPDATE academic_courses SET is_deleted = 1 WHERE id = $id"),
        'delete_intake'   => $conn->query("UPDATE academic_intakes SET is_deleted = 1 WHERE id = $id"),
        'delete_mode'     => $conn->query("UPDATE academic_modes SET is_deleted = 1 WHERE id = $id"),
        'hide_subject'    => $conn->query("UPDATE academic_subjects SET is_hidden = 1 WHERE id = $id"),
        'show_subject'    => $conn->query("UPDATE academic_subjects SET is_hidden = 0 WHERE id = $id"),
        'hide_offering'   => $conn->query("UPDATE course_offerings SET is_hidden = 1 WHERE id = $id"),
        'show_offering'   => $conn->query("UPDATE course_offerings SET is_hidden = 0 WHERE id = $id"),
        default => null
    };
    header('Location: admin_academic.php?msg=status_updated');
    exit();
}

if (isset($_GET['msg']) && $_GET['msg'] === 'status_updated') {
    $msg = "<div class='alert alert-success' style='color:green;background:#d4edda;padding:12px;border-radius:6px;margin-bottom:15px;font-weight:bold;'>System state context reconfigured successfully.</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_param') {
        $type = $_POST['type'] ?? '';
        $name = \App\Support\Validation::sanitizeString($_POST['name'] ?? '');
        
        if ($name !== '') {
            $table = match($type) { 'intake' => 'academic_intakes', 'mode' => 'academic_modes', 'course' => 'academic_courses', default => '' };
            if ($table !== '') {
                $stmt = $conn->prepare("INSERT IGNORE INTO $table (name) VALUES (?)");
                $stmt->bind_param('s', $name);
                $stmt->execute() ? ($msg = "<p style='color:green;font-weight:bold;'>Parameter Added!</p>") : ($msg = "<p style='color:red;font-weight:bold;'>Error writing record.</p>");
                $stmt->close();
            }
        }
    }

    if ($_POST['action'] === 'add_subject') {
        $courseId = (int)($_POST['course_id'] ?? 0);
        $subName = \App\Support\Validation::sanitizeString($_POST['subject_name'] ?? '');
        if ($courseId > 0 && $subName !== '') {
            $stmt = $conn->prepare("INSERT INTO academic_subjects (course_id, name) VALUES (?, ?)");
            $stmt->bind_param('is', $courseId, $subName);
            $stmt->execute() ? ($msg = "<p style='color:green;font-weight:bold;'>Subject Registered!</p>") : ($msg = "<p style='color:red;font-weight:bold;'>Error writing subject.</p>");
            $stmt->close();
        }
    }

    if ($_POST['action'] === 'map_offering') {
        $courseId  = (int)($_POST['course_id'] ?? 0);
        $intakes   = $_POST['intakes'] ?? [];
        $modes     = $_POST['modes'] ?? [];
        $subjects  = $_POST['subjects'] ?? [];

        if ($courseId > 0 && !empty($intakes) && !empty($modes) && !empty($subjects)) {
            $stmt = $conn->prepare("INSERT IGNORE INTO course_offerings (course_id, intake_id, mode_id, subject_id) VALUES (?, ?, ?, ?)");
            foreach ($intakes as $iId) {
                foreach ($modes as $mId) {
                    foreach ($subjects as $sId) {
                        $safeIntakeId  = (int)$iId;
                        $safeModeId    = (int)$mId;
                        $safeSubjectId = (int)$sId;
                        $stmt->bind_param('iiii', $courseId, $safeIntakeId, $safeModeId, $safeSubjectId);
                        $stmt->execute();
                    }
                }
            }
            $stmt->close();
            $msg = "<div class='alert alert-success' style='color:green;background:#d4edda;padding:12px;border-radius:6px;margin-bottom:15px;font-weight:bold;'>Matrix Offerings Configured Successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger' style='color:red;background:#f8d7da;padding:12px;border-radius:6px;margin-bottom:15px;font-weight:bold;'>Error: Complete all selections.</div>";
        }
    }
}

$intakes  = $conn->query("SELECT * FROM academic_intakes WHERE is_deleted = 0")->fetch_all(MYSQLI_ASSOC);
$modes    = $conn->query("SELECT * FROM academic_modes WHERE is_deleted = 0")->fetch_all(MYSQLI_ASSOC);
$courses  = $conn->query("SELECT * FROM academic_courses WHERE is_deleted = 0")->fetch_all(MYSQLI_ASSOC);
$subjects = $conn->query("SELECT s.*, c.name as c_name FROM academic_subjects s JOIN academic_courses c ON s.course_id = c.id WHERE c.is_deleted = 0")->fetch_all(MYSQLI_ASSOC);

$activeOfferings = $conn->query("
    SELECT co.id, co.is_hidden, c.name as course, i.name as intake, m.name as mode, s.name as subject 
    FROM course_offerings co
    JOIN academic_courses c ON co.course_id = c.id
    JOIN academic_intakes i ON co.intake_id = i.id
    JOIN academic_modes m ON co.mode_id = m.id
    JOIN academic_subjects s ON co.subject_id = s.id
    WHERE c.is_deleted = 0 AND i.is_deleted = 0 AND m.is_deleted = 0
    ORDER BY c.name ASC, co.id DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Configuration Matrix | Administration</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .dashboard-grid { display: flex; gap: 20px; flex-wrap: wrap; padding: 20px; font-family: sans-serif; }
        .panel-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); flex: 1; min-width: 300px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group select, .form-group input { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
        .checkbox-group { max-height: 120px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; border-radius: 4px; background: #fff; }
        .checkbox-label { display: block; margin-bottom: 6px; }
        .btn-submit { background: #0a4da2; color: #fff; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; width: 100%; font-weight: bold;}
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .badge-visible { background: #e8f8f5; color: #27ae60; }
        .badge-hidden { background: #fdf2e9; color: #e67e22; }
    </style>
</head>
<body style="background:#f4f7f6;">
    <div style="background:#0a4da2; padding:15px; color:#fff; display:flex; justify-content:space-between; align-items:center;">
        <h2>Academic Management Desk</h2>
        <a href="admin.php" style="color:#fff; text-decoration:none; font-weight:bold;">⬅ Back to Main Dashboard</a>
    </div>

    <div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
        <?= $msg ?>
        
        <div class="dashboard-grid">
            <div class="panel-card">
                <h3>1. Create Core Parameters</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_param">
                    <div class="form-group">
                        <label>Parameter Category</label>
                        <select name="type">
                            <option value="course">Program / Course Name</option>
                            <option value="intake">Intake Period</option>
                            <option value="mode">Study Mode</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Title Name</label>
                        <input type="text" name="name" required placeholder="e.g. BSc Cyber Security">
                    </div>
                    <button type="submit" class="btn-submit">Add Parameter</button>
                </form>
            </div>

            <div class="panel-card">
                <h3>2. Create Subjects</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_subject">
                    <div class="form-group">
                        <label>Target Course Link</label>
                        <select name="course_id">
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject Title</label>
                        <input type="text" name="subject_name" required placeholder="e.g. Object Oriented Analysis">
                    </div>
                    <button type="submit" class="btn-submit">Register Subject</button>
                </form>
            </div>

            <div class="panel-card" style="min-width: 100%;">
                <h3>3. Configure Matrix Course Offering Combinations</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="map_offering">
                    
                    <div class="form-group">
                        <label >Step 1: Select Program / Course Target</label>
                        <select name="course_id" id="matrix_course" required style="border: 2px solid;">
                            <option value="">-- Select Course --</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:15px;">
                        <div style="flex:1; min-width:250px;">
                            <label>Step 2: Map to Intake Periods</label>
                            <div class="checkbox-group">
                                <?php foreach ($intakes as $i): ?>
                                    <label class="checkbox-label"><input type="checkbox" name="intakes[]" value="<?= $i['id'] ?>"> <?= htmlspecialchars($i['name']) ?></label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div style="flex:1; min-width:250px;">
                            <label>Step 3: Map to Study Modes</label>
                            <div class="checkbox-group">
                                <?php foreach ($modes as $m): ?>
                                    <label class="checkbox-label"><input type="checkbox" name="modes[]" value="<?= $m['id'] ?>"> <?= htmlspecialchars($m['name']) ?></label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div style="flex:1; min-width:250px;">
                            <label>Step 4: Mapped Subjects Checklist</label>
                            <div class="checkbox-group" id="admin_subject_container" style="background:#f8f9fa; border:2px solid">
                                <span style="color:#7f8c8d; font-style:italic;">Please choose a course target in Step 1 first to unlock verified subjects.</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="background:#28a745; font-size:1rem;">Generate Safe Matrices Configuration</button>
                </form>
            </div>
        </div>

        <div class="panel-card" style="margin-top: 30px; font-family: sans-serif;">
            <h3>Academic Master Dictionary Management Desk</h3>
            <p style="color:#7f8c8d; font-size:0.85rem; margin-bottom:20px;">Manage global parameters state entries. Disabling or soft-deleting objects here guarantees perfect database cascading defense profiles.</p>
            
            <div style="display:flex; gap:20px; flex-wrap:wrap; font-size:0.9rem;">
                <div style="flex:1; min-width:250px; background:#fdfefe; padding:15px; border-radius:6px; border:1px solid #ddd;">
                    <h4>Programs / Courses</h4>
                    <?php foreach($courses as $c): ?>
                        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:8px; border-bottom:1px dashed #eee; padding-bottom:4px;">
                            <span style="flex:1;"><?= htmlspecialchars($c['name']) ?></span>
                            <a href="?toggle_status=delete_course&target_id=<?= $c['id'] ?>" style="color:#c0392b; text-decoration:none; font-weight:bold;" onclick="return confirm('Soft-delete this program completely?');">🗑️ Soft Delete</a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="flex:1; min-width:250px; background:#fdfefe; padding:15px; border-radius:6px; border:1px solid #ddd;">
                    <h4>Intake Horizons</h4>
                    <?php foreach($intakes as $i): ?>
                        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:8px; border-bottom:1px dashed #eee; padding-bottom:4px;">
                            <span style="flex:1;"><?= htmlspecialchars($i['name']) ?></span>
                            <a href="?toggle_status=delete_intake&target_id=<?= $i['id'] ?>" style="color:#c0392b; text-decoration:none; font-weight:bold;" onclick="return confirm('Soft-delete this intake row?');">🗑️ Soft Delete</a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="flex:1; min-width:250px; background:#fdfefe; padding:15px; border-radius:6px; border:1px solid #ddd;">
                    <h4>Study Modes</h4>
                    <?php foreach($modes as $m): ?>
                        <div style="display:flex; justify-content:between; align-items:center; margin-bottom:8px; border-bottom:1px dashed #eee; padding-bottom:4px;">
                            <span style="flex:1;"><?= htmlspecialchars($m['name']) ?></span>
                            <a href="?toggle_status=delete_mode&target_id=<?= $m['id'] ?>" style="color:#c0392b; text-decoration:none; font-weight:bold;" onclick="return confirm('Soft-delete this study mode?');">🗑️ Soft Delete</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <h4 style="margin-top:30px;">Subjects Inventory Control Index</h4>
            <table style="width:100%; border-collapse:collapse; margin-top:10px;">
                <tr style="background:#f8f9fa;">
                    <th style="padding:8px; border-bottom:1px solid #ddd;">Subject</th>
                    <th style="padding:8px; border-bottom:1px solid #ddd;">Belongs To</th>
                    <th style="padding:8px; border-bottom:1px solid #ddd;">State Status</th>
                    <th style="padding:8px; border-bottom:1px solid #ddd;">Controls</th>
                </tr>
                <?php foreach($subjects as $s): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:8px;"><?= htmlspecialchars($s['name']) ?></td>
                        <td style="padding:8px; color:#0a4da2; font-weight:bold;"><?= htmlspecialchars($s['c_name']) ?></td>
                        <td style="padding:8px;"><span class="badge <?= $s['is_hidden'] ? 'badge-hidden' : 'badge-visible' ?>"><?= $s['is_hidden'] ? 'Hided / Disabled' : 'Active View' ?></span></td>
                        <td style="padding:8px;">
                            <?php if($s['is_hidden']): ?>
                                <a href="?toggle_status=show_subject&target_id=<?= $s['id'] ?>" style="color:#27ae60; font-weight:bold; text-decoration:none;">👁️ Unhide View</a>
                            <?php else: ?>
                                <a href="?toggle_status=hide_subject&target_id=<?= $s['id'] ?>" style="color:#e67e22; font-weight:bold; text-decoration:none;">🚫 Hide Module</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="panel-card" style="min-width: 100%; margin-top:30px; font-family:sans-serif;">
            <h3>4. Configured Relational Offerings Combination Registry</h3>
            <table style="width:100%; border-collapse:collapse; margin-top:10px;">
                <thead>
                    <tr style="background:#f4f6fb; text-align:left;">
                        <th style="padding:12px; border-bottom:2px solid #ddd;">Course Program Link</th>
                        <th style="padding:12px; border-bottom:2px solid #ddd;">Intake Period</th>
                        <th style="padding:12px; border-bottom:2px solid #ddd;">Study Mode</th>
                        <th style="padding:12px; border-bottom:2px solid #ddd;">Mapped Subject Title</th>
                        <th style="padding:12px; border-bottom:2px solid #ddd;">Visibility Flag</th>
                        <th style="padding:12px; border-bottom:2px solid #ddd; text-align:center;">Operational Controls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($activeOfferings)): ?>
                        <?php foreach ($activeOfferings as $row): ?>
                            <tr style="border-bottom:1px solid #ddd; background: <?= $row['is_hidden'] ? '#fdf2e9' : '#fff' ?>;">
                                <td style="padding:12px; font-weight:bold; color:#0a4da2;"><?= htmlspecialchars($row['course']) ?></td>
                                <td style="padding:12px;"><?= htmlspecialchars($row['intake']) ?></td>
                                <td style="padding:12px;"><span style="background:#e8f8f5; color:#27ae60; padding:3px 8px; border-radius:4px; font-size:0.85rem; font-weight:bold;"><?= htmlspecialchars($row['mode']) ?></span></td>
                                <td style="padding:12px; font-style:italic;"><?= htmlspecialchars($row['subject']) ?></td>
                                <td style="padding:12px;"><span class="badge <?= $row['is_hidden'] ? 'badge-hidden' : 'badge-visible' ?>"><?= $row['is_hidden'] ? 'Hided' : 'Live' ?></span></td>
                                <td style="padding:12px; text-align:center;">
                                    <?php if($row['is_hidden']): ?>
                                        <a href="?toggle_status=show_offering&target_id=<?= $row['id'] ?>" style="background:#27ae60; color:#fff; text-decoration:none; padding:6px 12px; border-radius:4px; font-size:0.85rem; font-weight:bold;" onclick="return confirm('Unhide this matrix item back to student workspace pools?');">👁️ Unhide</a>
                                    <?php else: ?>
                                        <a href="?toggle_status=hide_offering&target_id=<?= $row['id'] ?>" style="background:#e67e22; color:#fff; text-decoration:none; padding:6px 12px; border-radius:4px; font-size:0.85rem; font-weight:bold;" onclick="return confirm('Hide this offering combination from student view rules?');">🚫 Hide Matrix</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:#95a5a6; font-weight:bold;">No multi-choice configuration matrices currently generated inside system data nodes.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.getElementById('matrix_course').addEventListener('change', function() {
        const courseId = this.value;
        const container = document.getElementById('admin_subject_container');
        
        if(!courseId) {
            container.innerHTML = '<span style="color:#7f8c8d; font-style:italic;">Please choose a course target in Step 1 first to unlock verified subjects.</span>';
            return;
        }

        container.innerHTML = '<span style="color:#0a4da2; font-weight:bold;">Loading matching course subjects payload...</span>';

        fetch(`admin_academic.php?get_subjects_by_course=${courseId}`)
            .then(res => res.json())
            .then(subjects => {
                if(subjects.length === 0) {
                    container.innerHTML = '<span style="color:red; font-weight:bold;">⚠️ Error: There are no active subjects registered to this specific course. Create some in panel 2 first!</span>';
                    return;
                }
                container.innerHTML = subjects.map(s => `
                    <label class="checkbox-label" style="color:#111; font-weight:normal; cursor:pointer; display:block; margin-bottom:6px;">
                        <input type="checkbox" name="subjects[]" value="${s.id}"> ${s.name}
                    </label>
                `).join('');
            });
    });
    </script>
</body>
</html>