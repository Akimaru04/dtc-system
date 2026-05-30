<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| FETCH USER (GET REQUEST)
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
| UPDATE USER (POST REQUEST)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF CHECK
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        set_flash("Security validation failed.", "error");
        header("Location: users.php");
        exit();
    }

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $role       = $_POST['role'] ?? '';
    $course     = trim($_POST['course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $status     = $_POST['account_status'] ?? '';

    if (!$first_name || !$last_name || !$email || !$role) {
        set_flash("Missing required fields.", "error");
    } else {

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
        } else {
            set_flash("Update failed.", "error");
        }

        $stmt->close();
    }
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

    <!-- HEADER -->
    <div class="card admin-header">
        <h1>Edit User</h1>
        <p>Update user information and account settings</p>
    </div>

    <!-- BACK BUTTON -->
    <div class="card action-bar">
        <a href="users.php" class="btn btn-secondary">
            ← Back to Users
        </a>
    </div>

    <!-- FORM -->
    <div class="card">

        <form method="POST" class="form-grid">

            <?= csrf_field() ?>

            <input type="text" name="first_name"
                   value="<?= htmlspecialchars($targetUser['first_name']) ?>"
                   placeholder="First Name" required>

            <input type="text" name="last_name"
                   value="<?= htmlspecialchars($targetUser['last_name']) ?>"
                   placeholder="Last Name" required>

            <input type="email" name="email"
                   value="<?= htmlspecialchars($targetUser['email']) ?>"
                   placeholder="Email" required>

            <select name="role" required>
                <option value="student" <?= $targetUser['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                <option value="registrar" <?= $targetUser['role'] === 'registrar' ? 'selected' : '' ?>>Registrar</option>
                <option value="admin" <?= $targetUser['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <input type="text" name="course"
                   value="<?= htmlspecialchars($targetUser['course']) ?>"
                   placeholder="Course">

            <input type="text" name="year_level"
                   value="<?= htmlspecialchars($targetUser['year_level']) ?>"
                   placeholder="Year Level">

            <select name="account_status">
                <option value="active" <?= $targetUser['account_status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $targetUser['account_status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit" class="btn btn-primary">
                Update User
            </button>

        </form>

    </div>

</div>

</body>
</html>