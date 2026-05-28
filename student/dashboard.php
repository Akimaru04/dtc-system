<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

// 🔐 middleware protection
$user = require_role(['student']);

// enforce password rule
enforce_password_change($user);

include('../includes/navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>

<h1>Student Dashboard</h1>

<p>Welcome, <?= htmlspecialchars($user['name']) ?>!</p>

<!-- STUDENT ACTIONS -->
<a href="request_document.php">
    <button>Request Document</button>
</a>

<a href="my_requests.php">
    <button>My Requests</button>
</a>

</body>
</html>