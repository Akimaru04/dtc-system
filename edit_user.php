<?php
// Start session for authentication
session_start();

// Include database connection
include('../config/connect.php');

// Include auth functions
include('../config/auth.php');

// Ensure user is logged in
checkAuth();

// Ensure only admin can access this page
requireRole('admin');

// Get user ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid user ID");
}

/* --------------------------
   FETCH EXISTING USER DATA
-------------------------- */
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found");
}

/* --------------------------
   UPDATE PROCESS
-------------------------- */
if (isset($_POST['update_user'])) {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $course = $_POST['course'];
    $year_level = $_POST['year_level'];
    $status = $_POST['account_status'];

    // Auto-handle non-student roles
    if ($role !== 'student') {
        $course = "N/A";
        $year_level = "N/A";
    }

    $stmt = $conn->prepare("
        UPDATE users
        SET first_name = ?,
            last_name = ?,
            email = ?,
            role = ?,
            course = ?,
            year_level = ?,
            account_status = ?
        WHERE user_id = ?
    ");

    $stmt->bind_param(
        "sssssssi",
        $first_name,
        $last_name,
        $email,
        $role,
        $course,
        $year_level,
        $status,
        $id
    );

    $stmt->execute();
    $stmt->close();

    // Redirect back to users page
    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>

<h2>Edit User</h2>

<a href="users.php">← Back to Users</a>

<br><br>

<!-- EDIT FORM -->
<form method="POST">

    <label>First Name</label><br>
    <input type="text" name="first_name"
           value="<?= htmlspecialchars($user['first_name']) ?>" required>
    <br><br>

    <label>Last Name</label><br>
    <input type="text" name="last_name"
           value="<?= htmlspecialchars($user['last_name']) ?>" required>
    <br><br>

    <label>Email</label><br>
    <input type="email" name="email"
           value="<?= htmlspecialchars($user['email']) ?>" required>
    <br><br>

    <label>Role</label><br>
    <select name="role">
        <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>Student</option>
        <option value="registrar" <?= $user['role'] == 'registrar' ? 'selected' : '' ?>>Registrar</option>
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>
    <br><br>

    <label>Course</label><br>
    <input type="text" name="course"
           value="<?= htmlspecialchars($user['course']) ?>">
    <br><br>

    <label>Year Level</label><br>
    <input type="text" name="year_level"
           value="<?= htmlspecialchars($user['year_level']) ?>">
    <br><br>

    <label>Status</label><br>
    <select name="account_status">
        <option value="active" <?= $user['account_status'] == 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $user['account_status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
    </select>
    <br><br>

    <button type="submit" name="update_user">
        Update User
    </button>

</form>

</body>
</html>