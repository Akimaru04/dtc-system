<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/dtc_system/');

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}

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

function currentUser() {
    return [
        "user_id" => $_SESSION['user_id'] ?? null,
        "role" => $_SESSION['role'] ?? null,
        "name" => $_SESSION['name'] ?? null
    ];
}