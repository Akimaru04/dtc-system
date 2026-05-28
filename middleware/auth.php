<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../config/connect.php');

define('BASE_URL', '/dtc_system/');

/*
|--------------------------------------------------------------------------
| GET CURRENT USER (DATABASE IS TRUTH)
|--------------------------------------------------------------------------
*/
function auth_user() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
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

    if ($user) {
        // build computed field
        $user['name'] = $user['first_name'] . ' ' . $user['last_name'];
    }

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
| REQUIRE ROLE(S)
|--------------------------------------------------------------------------
*/
function require_role($roles = []) {
    $user = auth_required();

    if (!in_array($user['role'], (array)$roles)) {
        session_destroy();
        header("Location: " . BASE_URL . "index.php");
        exit();
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