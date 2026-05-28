<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../config/connect.php');

define('BASE_URL', '/dtc_system/');

/*
|--------------------------------------------------------------------------
| GET CURRENT USER (TRUTH SOURCE = DATABASE)
|--------------------------------------------------------------------------
*/
function auth_user() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $conn->prepare("
        SELECT user_id, name, role, must_change_password
        FROM users
        WHERE user_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
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

    // optional security check (session hijack protection light layer)
    if (!isset($_SESSION['user_id'])) {
        session_destroy();
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