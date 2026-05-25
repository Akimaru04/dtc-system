<?php
session_start();

include('../config/connect.php');
include('../config/auth.php');

checkAuth();
requireRole('admin');

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid user ID");
}

/* --------------------------
   GENERATE NEW PASSWORD
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

<p style="color:green;">
    New Temporary Password:
    <strong><?= $temp_password ?></strong>
</p>

<a href="users.php">Back to Users</a>

</body>
</html>