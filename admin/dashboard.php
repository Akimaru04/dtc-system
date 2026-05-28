<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

// 🔐 middleware protection
$user = require_role(['admin']);

// enforce password rule
enforce_password_change($user);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h1>Admin Dashboard</h1>

<p>Welcome, <?= htmlspecialchars($user['name']) ?>!</p>

<p>Welcome to the admin dashboard! Here you can manage users, document types, and system settings.</p>

<!-- ADMIN ACTIONS -->
<a href="add_user.php">
    <button>Add User</button>
</a>

<a href="users.php">
    <button>Manage Users</button>
</a>

<a href="document_types.php">
    <button type="button">Manage Document Types</button>
</a>

</body>
</html>