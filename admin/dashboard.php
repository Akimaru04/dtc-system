<?php
session_start();

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;

require_once('../middleware/auth.php');
require_once('../includes/flash.php'); // ✅ ADD THIS

$user = require_role(['admin']);
enforce_password_change($user);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <div class="card admin-header">
        <h1>Admin Dashboard</h1>
        <p>System administration and user management</p>
    </div>

    <?php display_flash(); ?> <!-- ✅ ADD THIS -->

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