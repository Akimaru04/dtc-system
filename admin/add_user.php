<?php
session_start();

include(__DIR__ . '/../config/connect.php');
include(__DIR__ . '/../middleware/auth.php');

// 🔐 middleware protection
$user = require_role(['admin']);

$message = "";
$temp_password = "";

/* --------------------------
   ADD USER PROCESS
-------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {

    $student_number = trim($_POST['student_number'] ?? '');
    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $middle_name    = trim($_POST['middle_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $course         = trim($_POST['course'] ?? '');
    $year_level     = trim($_POST['year_level'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $role           = $_POST['role'] ?? '';

    /* --------------------------
       VALIDATION
    -------------------------- */
    if (!$student_number || !$first_name || !$last_name || !$email || !$role) {
        $message = "Please fill in all required fields.";

    } elseif (!in_array($role, ['student', 'registrar', 'admin'])) {
        $message = "Invalid role selected.";

    } else {

        /* --------------------------
           AUTO FIX FIELDS
        -------------------------- */
        if ($role !== 'student') {
            $course = "N/A";
            $year_level = "N/A";
        }

        $middle_name = $middle_name ?: "N/A";

        /* --------------------------
           GENERATE TEMP PASSWORD
        -------------------------- */
        $temp_password_plain = bin2hex(random_bytes(4));
        $hashed_password = password_hash($temp_password_plain, PASSWORD_DEFAULT);

        $status = "active";
        $must_change_password = 1;

        /* --------------------------
           INSERT USER (SECURE)
        -------------------------- */
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
</head>
<body>

<a href="dashboard.php"><button>Back to Dashboard</button></a>

<h2>Add User (Admin)</h2>

<?php if (!empty($message)): ?>
    <p style="color: green; font-weight: bold;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<?php if (!empty($temp_password)): ?>
    <p style="color: blue; font-weight: bold;">
        Temporary Password: <?= htmlspecialchars($temp_password) ?>
    </p>
<?php endif; ?>

<form method="POST">

    <input type="text" name="student_number" placeholder="Student/Staff ID" required><br><br>

    <input type="text" name="first_name" placeholder="First Name" required><br><br>

    <input type="text" name="last_name" placeholder="Last Name" required><br><br>

    <input type="text" name="middle_name" placeholder="Middle Name"><br><br>

    <input type="email" name="email" placeholder="Email" required><br><br>

    <input type="password" disabled placeholder="Auto-generated password"><br><br>

    <input type="text" name="course" placeholder="Course (students only)"><br><br>

    <input type="text" name="year_level" placeholder="Year Level (students only)"><br><br>

    <input type="text" name="contact_number" placeholder="Contact Number" required><br><br>

    <select name="role" required>
        <option value="">-- Select Role --</option>
        <option value="student">Student</option>
        <option value="registrar">Registrar</option>
        <option value="admin">Admin</option>
    </select>

    <br><br>

    <button type="submit" name="add_member">Add User</button>

</form>

</body>
</html>