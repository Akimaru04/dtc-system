<?php
session_start();

include(__DIR__ . '/../config/connect.php');
include(__DIR__ . '/../middleware/auth.php');

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| CSRF PROTECTION
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function verify_csrf() {
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }
}

$message = "";
$temp_password = "";

/*
|--------------------------------------------------------------------------
| ADD USER PROCESS
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {

    verify_csrf();

    $student_number = trim($_POST['student_number'] ?? '');
    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $middle_name    = trim($_POST['middle_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $course         = trim($_POST['course'] ?? '');
    $year_level     = trim($_POST['year_level'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $role           = $_POST['role'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */
    if (!$student_number || !$first_name || !$last_name || !$email || !$role) {
        $message = "Please fill in all required fields.";

    } elseif (!in_array($role, ['student', 'registrar', 'admin'], true)) {
        $message = "Invalid role selected.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | AUTO FIX FIELDS
        |--------------------------------------------------------------------------
        */
        if ($role !== 'student') {
            $course = "N/A";
            $year_level = "N/A";
        }

        $middle_name = $middle_name !== '' ? $middle_name : "N/A";

        /*
        |--------------------------------------------------------------------------
        | TEMP PASSWORD
        |--------------------------------------------------------------------------
        */
        $temp_password_plain = bin2hex(random_bytes(4));
        $hashed_password = password_hash($temp_password_plain, PASSWORD_DEFAULT);

        $status = "active";
        $must_change_password = 1;

        /*
        |--------------------------------------------------------------------------
        | INSERT USER
        |--------------------------------------------------------------------------
        */
        $stmt = $conn->prepare("
            INSERT INTO users 
            (student_number, first_name, last_name, middle_name, email, password, role, course, year_level, contact_number, account_status, must_change_password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssssssi",
            $student_number,
            $first_name,
            $last_name,
            $middle_name,
            $email,
            $hashed_password,
            $role,
            $course,
            $year_level,
            $contact_number,
            $status,
            $must_change_password
        );

        if ($stmt->execute()) {
            $message = "User created successfully!";
            $temp_password = $temp_password_plain;
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <div class="card admin-header">
        <h1>Add User</h1>
        <p>Create student, registrar, or admin accounts</p>
    </div>

    <div class="card action-bar">
        <a href="dashboard.php" class="btn btn-secondary">
            Back to Dashboard
        </a>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="card">
            <p><b><?= htmlspecialchars($message) ?></b></p>
        </div>
    <?php } ?>

    <?php if (!empty($temp_password)) { ?>
        <div class="card">
            <p style="color:#0d6efd;">
                Temporary Password: <b><?= htmlspecialchars($temp_password) ?></b>
            </p>
        </div>
    <?php } ?>

    <div class="card">

        <form method="POST" class="form-grid">

            <?= csrf_field() ?>

            <input type="text" name="student_number" placeholder="Student / Staff ID" required>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="middle_name" placeholder="Middle Name">
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="contact_number" placeholder="Contact Number" required>
            <input type="text" name="course" placeholder="Course (Students only)">
            <input type="text" name="year_level" placeholder="Year Level (Students only)">

            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="registrar">Registrar</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" name="add_member" class="btn btn-primary">
                Create User
            </button>

        </form>

    </div>

</div>

</body>
</html>