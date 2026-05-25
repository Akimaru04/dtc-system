<?php
session_start();

require_once(__DIR__ . '/../config/auth.php');

// 🔐 AUTH FLOW (ORDER IS IMPORTANT)
checkAuth();
requireRole('admin');
enforcePasswordChange();

include('../includes/navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h1>Admin Dashboard</h1>

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