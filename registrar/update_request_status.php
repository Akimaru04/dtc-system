<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');
include('../config/request_statuses.php');

require_role(['registrar']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_request.php");
    exit();
}

$request_id = intval($_POST['request_id'] ?? 0);
$new_status = $_POST['status'] ?? '';

if ($request_id <= 0 || !isset($STATUS_FLOW)) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_request.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| GET CURRENT STATUS
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("SELECT status FROM document_requests WHERE request_id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

if (!$current) {
    $_SESSION['error'] = "Request not found.";
    header("Location: manage_request.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| VALIDATE STATUS
|--------------------------------------------------------------------------
*/
if (!in_array($new_status, $REQUEST_STATUSES)) {
    $_SESSION['error'] = "Invalid status.";
    header("Location: manage_request.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| VALIDATE FLOW
|--------------------------------------------------------------------------
*/
if (!in_array($new_status, $STATUS_FLOW[$current['status']])) {
    $_SESSION['error'] = "Invalid transition.";
    header("Location: manage_request.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    UPDATE document_requests 
    SET status = ?
    WHERE request_id = ?
");

$stmt->bind_param("si", $new_status, $request_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Updated successfully.";
} else {
    $_SESSION['error'] = "Update failed.";
}

header("Location: manage_request.php");
exit();