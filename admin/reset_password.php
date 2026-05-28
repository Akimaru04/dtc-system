<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

// 🔐 middleware protection
$user = require_role(['admin']);

// validate ID safely
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid user ID");
}

// optional safety: prevent self-reset (recommended)
if ($id == $_SESSION['user_id']) {
    die("You cannot reset your own password.");
}

/* --------------------------
   GENERATE TEMP PASSWORD
-------------------------- */
$temp_password = bin2hex(random_bytes(4));
$hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

/* --------------------------
   UPDATE PASSWORD
-------------------------- */
$stmt = $conn->prepare("
    UPDATE users
    SET password = ?, must_change_password = 1
    WHERE user_id = ?
");

$stmt->bind_param("si", $hashed_password, $id);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>

<h2>Password Reset Successful</h2>

<p>Logged in as: <?= htmlspecialchars($user['name']) ?></p>

<p style="color:green;">
    New Temporary Password:
    <strong><?= htmlspecialchars($temp_password) ?></strong>
</p>

<a href="users.php">Back to Users</a>

</body>
</html>