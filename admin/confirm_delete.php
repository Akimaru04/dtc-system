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
| VALIDATE ID
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
| PREVENT SELF DELETE
|--------------------------------------------------------------------------
*/
$current_user_id = $_SESSION['user_id'] ?? null;

if (!$current_user_id) {

    set_flash("error", "Session expired. Please login again.");
    header("Location: ../index.php");
    exit();
}

if ($id == $current_user_id) {

    set_flash("error", "You cannot delete your own account.");
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| DELETE USER (SAFE)
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? LIMIT 1");

if (!$stmt) {

    set_flash("error", "Database error.");
    header("Location: users.php");
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {

    set_flash("success", "User deleted successfully.");

} else {

    set_flash("error", "Delete failed or user already removed.");
}

$stmt->close();

header("Location: users.php");
exit();
?>