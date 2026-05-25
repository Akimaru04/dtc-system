<?php
session_start();

require_once(__DIR__ . '/../config/auth.php');

// 🔐 AUTH FLOW (STANDARD ORDER)
checkAuth();
requireRole('registrar');
enforcePasswordChange();

include('../includes/navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Dashboard</title>
</head>
<body>

<h1>Registrar Dashboard</h1>

<p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</p>

<!-- REGISTRAR ACTIONS -->
<a href="manage_requests.php">
    <button>Manage Requests</button>
</a>

<a href="update_request.php">
    <button>Update Requests</button>
</a>

</body>
</html>