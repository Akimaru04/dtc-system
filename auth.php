<?php

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base project URL
define('BASE_URL', '/dtc_system/');

/* --------------------------
   CHECK LOGIN
-------------------------- */
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}

/* --------------------------
   ROLE PROTECTION
-------------------------- */
function requireRole($role) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}

/* --------------------------
   FORCE PASSWORD CHANGE
-------------------------- */
function enforcePasswordChange() {
    if (!empty($_SESSION['must_change_password']) && $_SESSION['must_change_password'] == 1) {
        header("Location: " . BASE_URL . "change_password.php");
        exit();
    }
}

/* --------------------------
   GET CURRENT USER
-------------------------- */
function currentUser() {
    return [
        "user_id" => $_SESSION['user_id'] ?? null,
        "role"    => $_SESSION['role'] ?? null,
        "name"    => $_SESSION['name'] ?? null
    ];
}