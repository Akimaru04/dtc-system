<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../config/connect.php');

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

    if ($user) {
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
| REQUIRE ROLE
|--------------------------------------------------------------------------
*/
function require_role($roles = []) {
    $user = auth_required();

    if (!in_array($user['role'], (array)$roles, true)) {
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

/*
|--------------------------------------------------------------------------
| FLASH MESSAGE HELPERS
|--------------------------------------------------------------------------
*/
function set_flash($message, $type = 'error') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function get_flash() {
    if (!empty($_SESSION['flash_message'])) {
        $flash = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type'] ?? 'info'
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);

        return $flash;
    }

    return null;
}

function redirect_with_flash($url, $message, $type = 'error') {
    set_flash($message, $type);
    header("Location: $url");
    exit();
}
?>