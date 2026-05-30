<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

/*
|--------------------------------------------------------------------------
| AUTH FIRST (MUST BE BEFORE ANY OUTPUT OR INCLUDES)
|--------------------------------------------------------------------------
*/
$user = require_role(['admin']);
enforce_password_change($user);

include('../includes/navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<div class="container">

    <div class="card admin-header">
        <h1>Admin Dashboard</h1>
        <p>System administration and user management</p>
    </div>

    <div class="card">
        <div class="admin-grid">

            <div class="card admin-card">
                <div>
                    <h3>User Accounts</h3>
                    <p>Create and manage student, registrar, and admin accounts.</p>
                </div>
                <a href="add_user.php" class="btn btn-primary">Add User</a>
            </div>

            <div class="card admin-card">
                <div>
                    <h3>User Management</h3>
                    <p>Edit, update roles, reset passwords, or deactivate accounts.</p>
                </div>
                <a href="users.php" class="btn btn-secondary">Manage Users</a>
            </div>

            <div class="card admin-card">
                <div>
                    <h3>Document System</h3>
                    <p>Manage official school document requirements.</p>
                </div>
                <a href="document_types.php" class="btn btn-success">Open</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>