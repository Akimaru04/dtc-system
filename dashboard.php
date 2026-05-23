<?php
require_once(__DIR__ . '/../config/auth.php');
checkAuth();
requireRole('student');

include('../includes/navbar.php');
?>

<h1>Student Dashboard</h1>