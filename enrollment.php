<?php
declare(strict_types=1);
require_once __DIR__ . '/app/bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($conn) || $conn === null) {
    $conn = $GLOBALS['conn'] ?? null;
}

\App\Middleware\RoleMiddleware::requireStudent('login.php');

if (isset($_GET['fetch_matrix'])) {
    header('Content-Type: application/json');
    
    global $conn;
    if (!$conn) { $conn = $GLOBALS['conn']; }
    
    $courseId = (int)($_GET['course_id'] ?? 0);
    $intakeId = (int)($_GET['intake_id'] ?? 0);
    $modeId   = (int)($_GET['mode_id'] ?? 0);
    
    $totalOfferings = $conn->query("SELECT COUNT(*) FROM course_offerings WHERE course_id = $courseId AND is_hidden = 0")->fetch_row()[0];

    $intakeQuery = "SELECT DISTINCT i.id, i.name FROM course_offerings co JOIN academic_intakes i ON co.intake_id = i.id WHERE co.course_id = $courseId AND co.is_hidden = 0 AND i.is_deleted = 0";
    if ($modeId > 0) {
        $intakeQuery .= " AND co.mode_id = $modeId";
    }
    $intakes = $conn->query($intakeQuery)->fetch_all(MYSQLI_ASSOC);
    
    $modeQuery = "SELECT DISTINCT m.id, m.name FROM course_offerings co JOIN academic_modes m ON co.mode_id = m.id WHERE co.course_id = $courseId AND co.is_hidden = 0 AND m.is_deleted = 0";
    if ($intakeId > 0) {
        $modeQuery .= " AND co.intake_id = $intakeId";
    }
    $modes = $conn->query($modeQuery)->fetch_all(MYSQLI_ASSOC);
    
    $subjects = [];
    if ($courseId > 0 && $intakeId > 0 && $modeId > 0) {
        $subjects = $conn->query("
            SELECT DISTINCT s.id, s.name 
            FROM course_offerings co 
            JOIN academic_subjects s ON co.subject_id = s.id 
            WHERE co.course_id = $courseId 
              AND co.intake_id = $intakeId 
              AND co.mode_id = $modeId 
              AND co.is_hidden = 0 
              AND s.is_hidden = 0
        ")->fetch_all(MYSQLI_ASSOC);
    }
    
    echo json_encode([
        'total_offerings' => (int)$totalOfferings,
        'intakes' => $intakes,
        'modes' => $modes,
        'subjects' => $subjects
    ]);
    exit();
}

$statusMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $conn;
    if (!$conn) { $conn = $GLOBALS['conn']; }

    $courseId  = (int)($_POST['course_id'] ?? 0);
    $intakeId  = (int)($_POST['intake_id'] ?? 0);
    $modeId    = (int)($_POST['mode_id'] ?? 0);
    $selectedSubs = $_POST['subjects'] ?? [];

    if ($courseId > 0 && $intakeId > 0 && $modeId > 0 && !empty($selectedSubs)) {
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        $fullName = $_SESSION['user']['full_name'] ?? 'Student';
        $email = $_SESSION['user']['email'] ?? '';

        $stmt = $conn->prepare("INSERT INTO enrollments (user_id, full_name, email) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $userId, $fullName, $email);
        $stmt->execute();
        $enrollmentId = $stmt->insert_id;
        $stmt->close();

        foreach ($selectedSubs as $subId) {
            $offeringQ = $conn->query("
                SELECT id FROM course_offerings 
                WHERE course_id = $courseId AND intake_id = $intakeId AND mode_id = $modeId AND subject_id = " . (int)$subId . " 
                AND is_hidden = 0 LIMIT 1
            ");
            if ($offering = $offeringQ->fetch_assoc()) {
                $coId = $offering['id'];
                $ins = $conn->prepare("INSERT INTO enrollment_items (enrollment_id, course_offering_id) VALUES (?, ?)");
                $ins->bind_param('ii', $enrollmentId, $coId);
                $ins->execute();
                $ins->close();
            }
        }
        
        $mailer = new \App\Support\MailerService();

        $enrollmentPayload = [
            'course_id' => $courseId,
            'intake_id' => $intakeId,
            'mode_id'   => $modeId,
            'subjects'  => $selectedSubs
        ];

        $mailer->sendEnrollmentConfirmation($fullName, $email, $enrollmentPayload, $conn);

        $statusMsg = "<div class='alert alert-success' style='color:green;background:#d4edda;padding:15px;border-radius:6px;font-weight:bold;'>Academic Multi-subject package enrolled smoothly! An HTML receipt confirmation has flown out to your mailbox.</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger' style='color:red;background:#f8d7da;padding:15px;border-radius:6px;font-weight:bold;'>Submission Denied: You must check at least one subject to complete your matrix payload.</div>";
    }
}

global $conn;
if (!$conn) { $conn = $GLOBALS['conn']; }

$coursesList = $conn->query("SELECT * FROM academic_courses WHERE is_deleted = 0")->fetch_all(MYSQLI_ASSOC);
?>

<?php \App\Support\Page::renderHead('Subject Enrollment Matrix Module'); ?>
<?php \App\Support\Page::renderStudentHeader('enrollment.php', 'Course Enrollment', 'Select your courses and configuration modules smoothly'); ?>

<section class="course" style="font-family:sans-serif; max-width:800px; margin:0 auto; padding:40px 20px;">
    <h1>Academic Enrollment Board</h1>
    <p>Dynamically manage your course selections and subjects matrix configuration limits.</p>
    
    <?= $statusMsg ?>

    <form method="POST" action="enrollment.php" style="background:#fff; padding:30px; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.08); text-align:left; margin-top:30px;">
        
        <div style="margin-bottom:20px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Select Academic Program / Course</label>
            <select name="course_id" id="course_dropdown" required style="width:100%; padding:10px; border-radius:4px; border:1px solid #ccc;">
                <option value="">-- Choose Course --</option>
                <?php foreach ($coursesList as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom:20px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Target Intake Term</label>
            <select name="intake_id" id="intake_dropdown" required disabled style="width:100%; padding:10px; border-radius:4px; border:1px solid #ccc; background:#e9ecef;"></select>
        </div>

        <div style="margin-bottom:20px;">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Preferred Mode of Study</label>
            <select name="mode_id" id="mode_dropdown" required disabled style="width:100%; padding:10px; border-radius:4px; border:1px solid #ccc; background:#e9ecef;"></select>
        </div>

        <div style="margin-bottom:25px;">
            <label style="font-weight:bold; display:block; margin-bottom:8px;">Available Subjects Matrix Checklist (Enroll Multi-Choice)</label>
            <div id="subjects_checkbox_container" style="border:1px solid #ccc; padding:15px; border-radius:4px; max-height:200px; overflow-y:auto; background:#f8f9fa; color:#666;">
                Select a course parameter above to compute available subject rows.
            </div>
        </div>

        <button type="submit" style="background:#0a4da2; color:#fff; border:none; width:100%; padding:15px; font-size:1.1rem; font-weight:bold; border-radius:6px; cursor:pointer;">Submit Enrollment Records Matrix</button>
    </form>
</section>

<script>
const courseDD = document.getElementById('course_dropdown');
const intakeDD = document.getElementById('intake_dropdown');
const modeDD   = document.getElementById('mode_dropdown');
const subBox   = document.getElementById('subjects_checkbox_container');

function calculateFinalSubjects() {
    const courseId = courseDD.value;
    const intakeId = intakeDD.value;
    const modeId   = modeDD.value;

    if (!courseId || !intakeId || !modeId) {
        subBox.innerHTML = '<span style="color:#7f8c8d; font-style:italic;">Please select your Target Intake and Study Mode completely to load active verified subjects.</span>';
        return;
    }

    subBox.innerHTML = '<span style="color:#0a4da2; font-weight:bold;">Computing verified subjects matrix registry logs...</span>';

    fetch(`enrollment.php?fetch_matrix=1&course_id=${courseId}&intake_id=${intakeId}&mode_id=${modeId}`)
        .then(res => res.json())
        .then(data => {
            if(data.subjects.length > 0) {
                subBox.innerHTML = data.subjects.map(s => `
                    <label style="display:block; margin-bottom:8px; font-weight:normal; color:#333; cursor:pointer;">
                        <input type="checkbox" name="subjects[]" value="${s.id}" style="margin-right:8px;"> ${s.name}
                    </label>
                `).join('');
            } else {
                subBox.innerHTML = '<span style="color:#c0392b; font-weight:bold;">⚠️ No active subjects available for this specific combination.</span>';
            }
        });
}

function refreshDropdownsLock(changedField) {
    const courseId = courseDD.value;
    const intakeId = intakeDD.value;
    const modeId   = modeDD.value;

    if (!courseId) return;

    fetch(`enrollment.php?fetch_matrix=1&course_id=${courseId}&intake_id=${intakeId}&mode_id=${modeId}`)
        .then(res => res.json())
        .then(data => {
            // 如果是在改变 Intake，我们需要动态刷新 Mode 的可选集，反之亦然
            if (changedField === 'intake' || changedField === 'course') {
                const currentModeValue = modeDD.value;
                modeDD.innerHTML = '<option value="">-- Choose Mode --</option>' + data.modes.map(m => `
                    <option value="${m.id}" ${m.id == currentModeValue ? 'selected' : ''}>${m.name}</option>
                `).join('');
            }
            
            if (changedField === 'mode' || changedField === 'course') {
                const currentIntakeValue = intakeDD.value;
                intakeDD.innerHTML = '<option value="">-- Choose Intake --</option>' + data.intakes.map(i => `
                    <option value="${i.id}" ${i.id == currentIntakeValue ? 'selected' : ''}>${i.name}</option>
                `).join('');
            }

            calculateFinalSubjects();
        });
}

courseDD.addEventListener('change', function() {
    const courseId = this.value;

    if (!courseId) {
        intakeDD.disabled = modeDD.disabled = true;
        intakeDD.innerHTML = modeDD.innerHTML = '';
        intakeDD.style.background = modeDD.style.background = '#e9ecef';
        subBox.innerHTML = 'Select a course parameter above to compute available subject rows.';
        return;
    }

    fetch(`enrollment.php?fetch_matrix=1&course_id=${courseId}`)
        .then(res => res.json())
        .then(data => {
            if (data.total_offerings === 0) {
                intakeDD.disabled = modeDD.disabled = true;
                intakeDD.innerHTML = modeDD.innerHTML = '';
                intakeDD.style.background = modeDD.style.background = '#e9ecef';
                subBox.innerHTML = '<div style="color:#c0392b; font-weight:bold; padding:5px; border:1px solid #fadbd8; background:#fdf2e9; border-radius:4px;">⚠️ ERROR: This academic program currently has NO subjects or offerings configured by the administration. You cannot proceed with enrollment for this course.</div>';
            } else {
                intakeDD.disabled = modeDD.disabled = false;
                intakeDD.style.background = modeDD.style.background = '#fff';
                
                refreshDropdownsLock('course');
            }
        });
});

intakeDD.addEventListener('change', function() {
    refreshDropdownsLock('intake');
});

modeDD.addEventListener('change', function() {
    refreshDropdownsLock('mode');
});
</script>

<?php \App\Support\Page::renderFooter(); ?>