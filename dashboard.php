<?php
require_once(__DIR__ . '/../config/auth.php');
checkAuth();
requireRole('admin');

include('../includes/navbar.php');
?>

<h1>Admin Dashboard</h1>