<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

$user = require_role(['admin']);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash("Invalid user ID.", "error");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| FETCH USER (SAFE)
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

/*
|--------------------------------------------------------------------------
| PREVENT SELF RESET (IMPORTANT SECURITY RULE)
|--------------------------------------------------------------------------
*/
if ($id == $_SESSION['user_id']) {
    set_flash("You cannot reset your own password.", "error");
    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <div class="card">

        <h2>Reset Password</h2>

        <p>
            Reset password for:
            <b><?= htmlspecialchars($targetUser['first_name'] . ' ' . $targetUser['last_name']) ?></b>
        </p>

        <div class="alert warning">
            This will generate a new temporary password for the user.
        </div>

        <form method="POST" action="confirm_reset.php">

            <!-- CSRF PROTECTION -->
            <?= csrf_field() ?>

            <input type="hidden" name="id" value="<?= (int)$id ?>">

            <button type="submit" class="btn btn-danger">
                Confirm Reset
            </button>

            <a href="users.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>