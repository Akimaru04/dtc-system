<?php
session_start();

include("../config/connect.php");
include("../middleware/auth.php");

require_role(['student']);

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$document_type_id = $_POST['document_type_id'] ?? null;
$purpose = $_POST['purpose'] ?? '';
$quantity = $_POST['quantity'] ?? 1;
$remarks = $_POST['remarks'] ?? '';

if (!$document_type_id || !$purpose) {
    die("Missing required fields.");
}

/* generate tracking code */
$tracking_code = uniqid('REQ-');

/*
|--------------------------------------------------------------------------
| INSERT REQUEST
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    INSERT INTO document_requests 
    (user_id, document_type_id, purpose, quantity, remarks, status, tracking_code)
    VALUES (?, ?, ?, ?, ?, 'pending_payment', ?)
");

$stmt->bind_param(
    "iisiss",
    $user_id,
    $document_type_id,
    $purpose,
    $quantity,
    $remarks,
    $tracking_code
);

if ($stmt->execute()) {
    header("Location: my_requests.php?success=1");
    exit;
} else {
    die("Insert failed: " . $stmt->error);
}