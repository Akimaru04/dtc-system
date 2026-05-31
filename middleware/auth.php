<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;
/*
|--------------------------------------------------------------------------
| BASE URL
|--------------------------------------------------------------------------
*/
define('BASE_URL', '/dtc-system-main/');

/*
|--------------------------------------------------------------------------
| GET CURRENT USER
|--------------------------------------------------------------------------
*/
function auth_user() {
    global $conn;

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $conn->prepare("
        SELECT user_id, first_name, last_name, role, must_change_password
        FROM users
        WHERE user_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Destroy invalid session if user no longer exists
    if (!$user) {
        session_unset();
        session_destroy();
        return null;
    }

    $user['name'] = trim($user['first_name'] . ' ' . $user['last_name']);

    return $user;
}

/*
|--------------------------------------------------------------------------
| REQUIRE LOGIN
|--------------------------------------------------------------------------
*/
function auth_required() {
    $user = auth_user();

    if (!$user) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }

    return $user;
}

/*
|--------------------------------------------------------------------------
| REQUIRE ROLE
|--------------------------------------------------------------------------
*/
function require_role($roles = []) {
    $user = auth_required();

    if (!in_array($user['role'], (array)$roles, true)) {
        http_response_code(403);
        die("403 Forbidden - Access denied.");
    }

    return $user;
}

/*
|--------------------------------------------------------------------------
| FORCE PASSWORD CHANGE
|--------------------------------------------------------------------------
*/
function enforce_password_change($user) {
    if (!empty($user['must_change_password'])) {
        header("Location: " . BASE_URL . "change_password.php");
        exit();
    }
}
?>