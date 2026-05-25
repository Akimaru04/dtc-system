<?php
session_start();

require_once(__DIR__ . '/../config/auth.php');

// 🔐 AUTH CHECKS (ORDER MATTERS)
checkAuth();
requireRole('student');
enforcePasswordChange();

include('../includes/navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>

<h1>Student Dashboard</h1>

<p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</p>

<!-- STUDENT ACTIONS -->
<a href="request_document.php">
    <button>Request Document</button>
</a>

<a href="my_requests.php">
    <button>My Requests</button>
</a>

</body>
</html>