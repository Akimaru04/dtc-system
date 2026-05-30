<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');
include('../includes/csrf.php');

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/*
|--------------------------------------------------------------------------
| VALIDATE ID
|--------------------------------------------------------------------------
*/
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash("Invalid user ID.", "error");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| PREVENT SELF DELETE
|--------------------------------------------------------------------------
*/
if ($id == $_SESSION['user_id']) {
    set_flash("You cannot delete your own account.", "error");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| FETCH USER
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT user_id, first_name, last_name 
    FROM users 
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();

$targetUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$targetUser) {
    set_flash("User not found.", "error");
    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete User</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <div class="card">

        <h2>Delete User</h2>

        <p>
            Are you sure you want to delete:
            <b><?= htmlspecialchars($targetUser['first_name'] . ' ' . $targetUser['last_name']) ?></b>?
        </p>

        <div class="alert warning">
            ⚠ This action cannot be undone.
        </div>

        <form method="POST" action="confirm_delete.php">

            <?= csrf_field() ?>

            <input type="hidden" name="id" value="<?= $id ?>">

            <button type="submit" class="btn btn-danger">
                Confirm Delete
            </button>

            <a href="users.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>