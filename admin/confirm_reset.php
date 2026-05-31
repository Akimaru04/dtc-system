<?php
session_start();

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;

require_once('../middleware/auth.php');
require_once('../includes/csrf.php');
require_once('../includes/flash.php'); // ✅ REQUIRED

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| POST ONLY GUARD
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    set_flash("error", "Invalid request method.");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| CSRF CHECK
|--------------------------------------------------------------------------
*/
verify_csrf();

/*
|--------------------------------------------------------------------------
| VALIDATE INPUT
|--------------------------------------------------------------------------
*/
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {

    set_flash("error", "Invalid request.");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| SESSION SAFETY CHECK
|--------------------------------------------------------------------------
*/
$current_user_id = $_SESSION['user_id'] ?? null;

if (!$current_user_id) {

    set_flash("error", "Session expired. Please login again.");
    header("Location: ../index.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| PREVENT SELF RESET
|--------------------------------------------------------------------------
*/
if ($id == $current_user_id) {

    set_flash("error", "You cannot reset your own password.");
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

if (!$stmt) {

    set_flash("error", "Database error.");
    header("Location: users.php");
    exit();
}

$stmt->bind_param("si", $hashed, $id);
$success = $stmt->execute();

$stmt->close();

/*
|--------------------------------------------------------------------------
| RESULT HANDLING
|--------------------------------------------------------------------------
*/
if ($success) {

    set_flash(
        "success",
        "Password reset successful. Temporary password: " . $temp_password
    );

} else {

    set_flash("error", "Password reset failed.");
}

header("Location: users.php");
exit();
?>