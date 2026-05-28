<?php
session_start();
include('../config/connect.php');
include('../config/auth.php');

checkAuth(); // ensure only registrar/admin can access

// -----------------------------
// VALIDATE REQUEST METHOD
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_request.php");
    exit();
}

// -----------------------------
// GET DATA
// -----------------------------
$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

// -----------------------------
// VALIDATE INPUT
// -----------------------------
$allowed_status = ['Pending', 'Approved', 'Processing', 'Released', 'Rejected'];

if ($request_id <= 0 || !in_array($status, $allowed_status)) {
    $_SESSION['error'] = "Invalid request update.";
    header("Location: manage_request.php");
    exit();
}

// -----------------------------
// UPDATE STATUS
// -----------------------------
$stmt = $conn->prepare("
    UPDATE document_requests 
    SET status = ?, updated_at = NOW() 
    WHERE request_id = ?
");

$stmt->bind_param("si", $status, $request_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Request updated successfully.";
} else {
    $_SESSION['error'] = "Failed to update request.";
}

// -----------------------------
// REDIRECT BACK
// -----------------------------
header("Location: manage_request.php");
exit();
?>