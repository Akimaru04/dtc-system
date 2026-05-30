<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');
include('../includes/csrf.php');

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| POST ONLY GUARD
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash("Invalid request method.", "error");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| CSRF PROTECTION
|--------------------------------------------------------------------------
*/
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    set_flash("Security validation failed.", "error");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| VALIDATE INPUT
|--------------------------------------------------------------------------
*/
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash("Invalid request.", "error");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| PREVENT SELF RESET
|--------------------------------------------------------------------------
*/
if ($id == $_SESSION['user_id']) {
    set_flash("You cannot reset your own password.", "error");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| GENERATE TEMP PASSWORD
|--------------------------------------------------------------------------
*/
$temp_password = bin2hex(random_bytes(4));
$hashed = password_hash($temp_password, PASSWORD_DEFAULT);

/*
|--------------------------------------------------------------------------
| UPDATE PASSWORD
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    UPDATE users
    SET password = ?, must_change_password = 1
    WHERE user_id = ?
    LIMIT 1
");

$stmt->bind_param("si", $hashed, $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    set_flash("Password reset successful. Temporary password: $temp_password", "success");
} else {
    set_flash("No user was updated.", "error");
}

$stmt->close();

header("Location: users.php");
exit();