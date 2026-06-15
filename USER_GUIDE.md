# 🎓 University Portal - Enterprise Software Operation & Lifecycle Architecture Manual

This official manual describes the operational workflows, underlying system-state shifts, and defensive guard mechanics engineered within the `universityportalenchance` system. It is structured specifically to support code evaluation, administrative routine auditing, and future perfective maintenance cycles.

---

## 👨‍🎓 PART 1: STUDENT INTERACTIVE ENROLLMENT LIFECYCLE

### 1.1 Pre-conditions & State Requirements
Before a student can initiate an academic application transaction, the following environment states must be validated:
1.  **Authentication Guard:** The client must be logged in. The security middleware `RoleMiddleware::requireStudent()` filters incoming traffic, intercepting unauthorized direct URL access attempts and dropping intruders back to `login.php`.
2.  **Identity Array Binding:** The authentication layer (`Auth.php`) must successfully cast the profile's unique record identifier to a strict database integer `(int)$user['id']` and persist it within the secure `$_SESSION['user']` associative cluster.
3.  **Active Curriculum offering:** At least one academic program matrix combination must be set to visible (`is_hidden = 0`) by the administrative staff inside the `course_offerings` schema table.

### 1.2 Step-by-Step Enrollment Execution Pattern

#### Step 1: Navigating to the Academic Board
Log into your verified account profile and select **Course Enrollment** from the header navigation bar, or execute a direct request to access the `enrollment.php` file path.

#### Step 2: Selecting an Academic Program Major
Click the **Select Academic Program / Course** dropdown selection panel. This dynamically queries the `academic_courses` table where `is_deleted = 0`.
* **Defensive Guard Trigger (Empty State Interception):** If you select a program that currently has zero course configuration relationships published in the database back-end, an asynchronous JavaScript `fetch()` request immediately catches the anomaly via `data.total_offerings === 0`. 
* **System Action:** The **Target Intake Term** and **Preferred Mode of Study** selection boxes are instantly set to `disabled`, their CSS backgrounds freeze to a locked gray state (`#e9ecef`), and a prominent warning alert is rendered within the checkbox section:
    `⚠️ ERROR: This academic program currently has NO subjects or offerings configured by the administration. You cannot proceed with enrollment for this course.`

#### Step 3: Engaging the Three-Dimensional Bidirectional Interlocking Filters
If the selected course contains published offerings, the dropdowns unlock. The system avoids invalid form submissions by using a bidirectional cascading filtering script:
* **Workflow Path A (Intake Term First):** Select an active intake period from the **Target Intake Term** list. The system fires an asynchronous network payload back to `enrollment.php?fetch_matrix=1`. The response isolates matching rows, and the JavaScript method `refreshDropdownsLock('intake')` updates the **Preferred Mode of Study** options to display *only* the modes running during that semester.
* **Workflow Path B (Study Mode First):** Alternatively, select a learning layout from the **Preferred Mode of Study** list first. The script catches the action via an event listener, updates the variables, and prunes the **Target Intake Term** select list to display *only* the semesters that offer that specific mode.

#### Step 4: Multi-Subject Choice Checklist Processing
1.  Once all three parameter options contain cross-referenced selections, the checkbox container dynamically replaces its loading text with active subject listings matching the selected matrix.
2.  Review the subject rows. Check the boxes next to all the specific units you intend to register for concurrently within this academic package envelope.
3.  Click the blue **Submit Enrollment Records Matrix** button.

#### Step 5: Automated Relational Audit Receipt Delivery
1.  The server processes the payload using parameterized SQL prepared statements to insert data into the parent `enrollments` table, grabs the auto-generated unique reference key (`$stmt->insert_id`), and inserts separate rows into the `enrollment_items` child log table.
2.  A green notification bar confirms successful processing: `Academic Multi-subject package enrolled smoothly! An HTML receipt confirmation has flown out to your mailbox.`
3.  The backend notification service (`MailerService.php`) automatically executes database lookups to convert numerical keys into human-readable text. It connects over encrypted TLS SMTP relays to send a professionally styled HTML confirmation letter containing all chosen courses, intakes, modes, and subjects directly to your registered inbox.

---

## 👨‍💼 PART 2: ADMINISTRATIVE ACADEMIC MANAGEMENT ARCHITECTURE

### 2.1 Pre-conditions & Access Requirements
To perform maintenance tasks on the university data lookup matrices, administrators must meet these security conditions:
1.  **RBAC Verification:** The user session must match administrative privileges. Any unauthenticated guest requests targeting administrative files are blocked by `RoleMiddleware::requireAdmin()` and redirected to `admin_login.php`.
2.  **Global Connection Alignment:** The administrator panel relies on explicit全域 variable alignment handlers to ensure the central connection pointer (`$conn`) never drops into a `null` state during nested script operations.

### 2.2 Step-by-Step Administrative Workflows

#### Task 1: Provisioning Baseline Dictionary Items
To expand the academic offering capabilities, administrators can add fresh entries to the base dictionary lookups:
1.  Navigate to the **Academic Matrix Board** console workspace layout via `admin_academic.php`.
2.  Locate the respective component control card: **Add New Course Program, Add New Intake Term, Add New Mode, or Add New Subject Unit**.
3.  Input the plain text identifier (e.g., *Subject Name: Artificial Intelligence Basics*) into the target input field.
4.  *Security Check:* The input string is automatically intercepted and stripped of malicious HTML injection tags by `Validation::sanitizeString()` before data serialization.
5.  Click **Add**. The entry immediately displays in the control log grid below.

#### Task 2: Orchestrating an Academic Offering Matrix Combo
To link independent components into a valid, selectable choice path for student use:
1.  Scroll to the **Orchestrate New Course Offering Combo** configuration panel section.
2.  Select a target program from the **Target Course** dropdown menu.
3.  Hold down the control key to select multiple items from the array lists for **Target Intakes**, **Preferred Modes**, and **Available Subject Units**.
4.  Click **Publish Offering Matrix Combination**.
5.  *Under the Hood:* The system loops through the selected multidimensional arrays. It applies type-casting variable assignments to satisfy pass-by-reference constraints, safely generating intersection maps across all chosen records in the `course_offerings` table.

#### Task 3: Managing Visibility & Executing Soft Deletes
1.  To temporarily hide an active offering combination from students without breaking historical logs, click the **Toggle Visibility** action button on the target data row. This changes the row's state flag (`is_hidden = 1`) instantly.
2.  To remove an item permanently from student view configurations, click the red **Delete** action link.
3.  **Defensive UX Interceptor Gate:** The browser intercepts the deletion execution path and prompts the admin with a modal dialog confirmation window. The operation will execute and update the non-destructive state machine flag (`is_deleted = 1`) *only* if the administrator clicks **OK**. This prevents historical data-warehouse logs from being accidentally broken or deleted.

---

## 🛑 PART 3: ARCHITECTURAL FAULT-DIAGNOSIS & SYSTEM ERROR DICTIONARY

When modifying environment variables (`.env`) or changing system runtime configurations, use this diagnosis index to resolve structural faults:

### Error Code 0x01: Relational Foreign Key Constraint Failure
* **Symptom:** `Fatal error: Uncaught mysqli_sql_exception: Cannot add or update a child row: a foreign key constraint fails...`
* **Root Cause:** The authentication routing layer completed a student login but mapped the target session identifier `$_SESSION['user']` to a flat string literal `'yes'`, instead of an active data array. Consequently, `enrollment.php` read a student ID of `0`, which failed parent-child validation checks when inserting records into the database.
* **Remedy:** Clear your browser cache session state, update `Auth.php` to use the structured associative session array mapping method, and log back into the system.

### Error Code 0x02: Global Connection Scope Dropout
* **Symptom:** `Fatal error: Uncaught Error: Call to a member function prepare() / query() on null...`
* **Root Cause:** Deeply nesting page layouts via `require_once` statements caused localized scope isolation within your PHP installation, dropping or clearing the active database connection variable `$conn` midway through script execution.
* **Remedy:** Add the explicit synchronization fallback declaration block to the absolute top of the failing script file:
    ```php
    global $conn; if (!isset($conn)) { $conn = $GLOBALS['conn'] ?? null; }
    ```

### Error Code 0x03: Cryptographic Token Token Truncation (Silent SMTP Failure)
* **Symptom:** Forms submit and save records to the database smoothly, but outbound registration or enrollment confirmation emails fail to arrive in student inboxes.
* **Root Cause:** The 16-character third-party application password token contains blank whitespace sequences (e.g., `nviq yuwr qgxb dmou`). When parsed without string boundary markers, the parser treats whitespace as line breaks, truncating your secret key during ingestion.
* **Remedy:** Open the root `.env` layout file and wrap the multi-word secret parameter securely within double quotes:
    `MAIL_PASSWORD="abcd efgh ijkl"`
