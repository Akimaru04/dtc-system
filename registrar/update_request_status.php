<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

// 🔐 secure access (registrar only)
$user = require_role(['registrar']);

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
$request_id = intval($_POST['request_id'] ?? 0);
$new_status  = trim($_POST['status'] ?? '');

// -----------------------------
// ALLOWED STATUSES
// -----------------------------
$allowed_status = ['Pending', 'Processing', 'Ready for Claiming', 'Claimed', 'Rejected'];

if ($request_id <= 0 || !in_array($new_status, $allowed_status)) {
    $_SESSION['error'] = "Invalid request update.";
    header("Location: manage_request.php");
    exit();
}

// -----------------------------
// GET CURRENT STATUS (IMPORTANT FOR SECURITY)
// -----------------------------
$stmt = $conn->prepare("SELECT status FROM document_requests WHERE request_id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    $_SESSION['error'] = "Request not found.";
    header("Location: manage_request.php");
    exit();
}

$current_status = $row['status'];

// -----------------------------
// STATUS FLOW RULE (CORE SECURITY)
// -----------------------------
$flow = [
    "Pending" => ["Processing", "Rejected"],
    "Processing" => ["Ready for Claiming", "Rejected"],
    "Ready for Claiming" => ["Claimed"],
    "Rejected" => [],
    "Claimed" => []
];

// -----------------------------
// VALIDATE TRANSITION
// -----------------------------
if (!in_array($new_status, $flow[$current_status])) {
    $_SESSION['error'] = "Invalid status transition.";
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

$stmt->bind_param("si", $new_status, $request_id);

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