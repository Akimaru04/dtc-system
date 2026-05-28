<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

// 🔐 admin-only access
$user = require_role(['admin']);

// validate ID safely
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid user ID");
}

// OPTIONAL SAFETY CHECK (recommended)
// prevent deleting yourself
if ($id == $_SESSION['user_id']) {
    die("You cannot delete your own account.");
}

/*
|--------------------------------------------------------------------------
| DELETE USER (SAFE QUERY)
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// redirect
header("Location: users.php?success=User deleted successfully");
exit();