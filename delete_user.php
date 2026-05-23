<?php
require_once '../auth.php';
checkAuth();
requireRole('admin');

include('../config/connect.php');

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM users WHERE user_id=$id");

header("Location: users.php");
exit();