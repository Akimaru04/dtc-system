<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "dtc_system";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("DB CONNECTION FAILED: " . mysqli_connect_error());
}
?>