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
| CSRF CHECK
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
| VALIDATE ID
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
| DELETE USER (SAFE)
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    set_flash("User deleted successfully.", "success");
} else {
    set_flash("Delete failed or user already removed.", "error");
}

$stmt->close();

header("Location: users.php");
exit();