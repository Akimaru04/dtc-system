<?php
include("../config/connect.php");
include("../config/auth.php");

requireRole("registrar");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    $sql = "UPDATE document_requests 
            SET request_status = '$status'
            WHERE request_id = '$request_id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_requests.php?success=1");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>