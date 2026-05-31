<?php
session_start();

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;
include('../middleware/auth.php');

// 🔐 middleware protection
$user = require_role(['student']);

// enforce password rule
enforce_password_change($user);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/student.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <!-- HEADER -->
    <div class="card student-header">
        <h1>Student Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($user['name']) ?>!</p>
    </div>

    <!-- DASHBOARD GRID -->
    <div class="student-grid">

        <!-- REQUEST DOCUMENT -->
        <div class="card student-card">
            <h2>Request Document</h2>
            <p>Submit a new document request to the registrar.</p>

            <div class="student-card-actions">
                <a href="request_document.php">
                    <button class="btn" style="background:#0d6efd; color:white;">
                        Go
                    </button>
                </a>
            </div>
        </div>

        <!-- MY REQUESTS -->
        <div class="card student-card">
            <h2>My Requests</h2>
            <p>Track the status of your submitted documents.</p>

            <div class="student-card-actions">
                <a href="my_requests.php">
                    <button class="btn" style="background:#6c757d; color:white;">
                        View
                    </button>
                </a>
            </div>
        </div>

    </div>

</div>

</body>
</html>