<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

// 🔐 middleware protection
$user = require_role(['admin']);

// Get user ID from URL
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid user ID");
}

/*
|--------------------------------------------------------------------------
| FETCH USER
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$targetUser = $result->fetch_assoc();
$stmt->close();

if (!$targetUser) {
    die("User not found");
}

/*
|--------------------------------------------------------------------------
| UPDATE USER
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_user'])) {

    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $role         = $_POST['role'] ?? '';
    $course       = trim($_POST['course'] ?? '');
    $year_level   = trim($_POST['year_level'] ?? '');
    $status       = $_POST['account_status'] ?? '';

    // Basic validation
    if (!$first_name || !$last_name || !$email || !$role) {
        die("Missing required fields");
    }

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

    header("Location: users.php?success=User updated successfully");
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

<p>Logged in as: <?= htmlspecialchars($user['name']) ?></p>

<a href="users.php">← Back to Users</a>

<br><br>

<form method="POST">

    <label>First Name</label><br>
    <input type="text" name="first_name"
           value="<?= htmlspecialchars($targetUser['first_name']) ?>" required>
    <br><br>

    <label>Last Name</label><br>
    <input type="text" name="last_name"
           value="<?= htmlspecialchars($targetUser['last_name']) ?>" required>
    <br><br>

    <label>Email</label><br>
    <input type="email" name="email"
           value="<?= htmlspecialchars($targetUser['email']) ?>" required>
    <br><br>

    <label>Role</label><br>
    <select name="role">
        <option value="student" <?= $targetUser['role'] == 'student' ? 'selected' : '' ?>>Student</option>
        <option value="registrar" <?= $targetUser['role'] == 'registrar' ? 'selected' : '' ?>>Registrar</option>
        <option value="admin" <?= $targetUser['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <br><br>

    <label>Course</label><br>
    <input type="text" name="course"
           value="<?= htmlspecialchars($targetUser['course']) ?>">

    <br><br>

    <label>Year Level</label><br>
    <input type="text" name="year_level"
           value="<?= htmlspecialchars($targetUser['year_level']) ?>">

    <br><br>

    <label>Status</label><br>
    <select name="account_status">
        <option value="active" <?= $targetUser['account_status'] == 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $targetUser['account_status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
    </select>

    <br><br>

    <button type="submit" name="update_user">Update User</button>

</form>

</body>
</html>