<?php
session_start();

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;

require_once('../middleware/auth.php');
require_once('../includes/csrf.php');
require_once('../includes/flash.php'); // ✅ FIXED

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| FETCH USER
|--------------------------------------------------------------------------
*/
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {

    set_flash("error", "Invalid user ID.");
    header("Location: users.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();

$targetUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$targetUser) {

    set_flash("error", "User not found.");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| SELF EDIT CHECK
|--------------------------------------------------------------------------
*/
$is_self = ($targetUser['user_id'] == $_SESSION['user_id']);

/*
|--------------------------------------------------------------------------
| UPDATE USER
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $role       = $_POST['role'] ?? '';
    $course     = trim($_POST['course'] ?? '');
    $year_level = trim($_POST['year_level'] ?? '');
    $status     = $_POST['account_status'] ?? '';

    if (!$first_name || !$last_name || !$email || !$role) {

        set_flash("error", "Missing required fields.");
        header("Location: edit_user.php?id=$id");
        exit();
    }

    /*
    |--------------------------------------------------------------------------
    | EMAIL DUPLICATE CHECK
    |--------------------------------------------------------------------------
    */
    $check = $conn->prepare("
        SELECT user_id 
        FROM users 
        WHERE email = ? AND user_id != ?
        LIMIT 1
    ");

    $check->bind_param("si", $email, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {

        set_flash("error", "Email already in use.");
        header("Location: edit_user.php?id=$id");
        exit();
    }

    $check->close();

    /*
    |--------------------------------------------------------------------------
    | SELF-EDIT PROTECTION (FIXED SAFETY)
    |--------------------------------------------------------------------------
    */
    if ($is_self) {
        $role = $targetUser['role'];
        $status = $targetUser['account_status'];
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO RULES
    |--------------------------------------------------------------------------
    */
    if ($role !== 'student') {
        $course = "N/A";
        $year_level = "N/A";
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE QUERY
    |--------------------------------------------------------------------------
    */
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

        set_flash("success", "User updated successfully.");
        header("Location: users.php");
        exit();

    } else {

        set_flash("error", "Update failed: " . $stmt->error);
        header("Location: edit_user.php?id=$id");
        exit();
    }

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

    <?php if ($is_self): ?>
        <div class="card">
            <div class="alert warning">
                You are editing your own account. Role and status cannot be changed.
            </div>
        </div>
    <?php endif; ?>

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

            <select name="role" required <?= $is_self ? 'disabled' : '' ?>>
                <option value="student" <?= ($targetUser['role'] ?? '') === 'student' ? 'selected' : '' ?>>Student</option>
                <option value="registrar" <?= ($targetUser['role'] ?? '') === 'registrar' ? 'selected' : '' ?>>Registrar</option>
                <option value="admin" <?= ($targetUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <input type="text" name="course"
                   value="<?= htmlspecialchars($targetUser['course'] ?? '') ?>">

            <input type="text" name="year_level"
                   value="<?= htmlspecialchars($targetUser['year_level'] ?? '') ?>">

            <select name="account_status" <?= $is_self ? 'disabled' : '' ?>>
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