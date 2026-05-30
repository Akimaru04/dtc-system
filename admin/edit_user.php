<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');
include('../includes/csrf.php');

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| FETCH USER
|--------------------------------------------------------------------------
*/
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash("Invalid user ID.", "error");
    header("Location: users.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();

$targetUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$targetUser) {
    set_flash("User not found.", "error");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| UPDATE USER
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF CHECK (USE YOUR CENTRAL FUNCTION)
    verify_csrf();

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $role       = $_POST['role'] ?? '';
    $course     = trim($_POST['course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $status     = $_POST['account_status'] ?? '';

    if (!$first_name || !$last_name || !$email || !$role) {
        set_flash("Missing required fields.", "error");
        header("Location: edit_user.php?id=$id");
        exit();
    }

    // enforce rule
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

    if ($stmt->execute()) {
        set_flash("User updated successfully.", "success");
        header("Location: users.php");
        exit();
    }

    set_flash("Update failed.", "error");
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <div class="card admin-header">
        <h1>Edit User</h1>
        <p>Update user information and account settings</p>
    </div>

    <div class="card action-bar">
        <a href="users.php" class="btn btn-secondary">
            ← Back to Users
        </a>
    </div>

    <div class="card">

        <form method="POST" class="form-grid">

            <?= csrf_field() ?>

            <input type="text" name="first_name"
                   value="<?= htmlspecialchars($targetUser['first_name'] ?? '') ?>"
                   required>

            <input type="text" name="last_name"
                   value="<?= htmlspecialchars($targetUser['last_name'] ?? '') ?>"
                   required>

            <input type="email" name="email"
                   value="<?= htmlspecialchars($targetUser['email'] ?? '') ?>"
                   required>

            <select name="role" required>
                <option value="student" <?= ($targetUser['role'] ?? '') === 'student' ? 'selected' : '' ?>>Student</option>
                <option value="registrar" <?= ($targetUser['role'] ?? '') === 'registrar' ? 'selected' : '' ?>>Registrar</option>
                <option value="admin" <?= ($targetUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <input type="text" name="course"
                   value="<?= htmlspecialchars($targetUser['course'] ?? '') ?>">

            <input type="text" name="year_level"
                   value="<?= htmlspecialchars($targetUser['year_level'] ?? '') ?>">

            <select name="account_status">
                <option value="active" <?= ($targetUser['account_status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($targetUser['account_status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit" class="btn btn-primary">
                Update User
            </button>

        </form>

    </div>

</div>

</body>
</html>