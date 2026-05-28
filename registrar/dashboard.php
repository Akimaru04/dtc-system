<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

// 🔐 secure registrar-only access
$user = require_role(['registrar']);

// enforce password change rule
enforce_password_change($user);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Dashboard</title>
</head>
<body>

<a href="../logout.php"><button>Logout</button></a>

<h1>Registrar Dashboard</h1>

<p>Welcome, <?= htmlspecialchars($user['name']) ?>!</p>

<!-- REGISTRAR ACTIONS -->
<a href="manage_request.php">
    <button>Manage Requests</button>
</a>

<a href="update_request_status.php">
    <button>Update Requests</button>
</a>

</body>
</html>