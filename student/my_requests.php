<?php
session_start();

include("../config/connect.php");
include("../middleware/auth.php");

// student-only access
require_role(['student']);

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| FETCH STUDENT REQUESTS
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT 
        dr.request_id,
        dr.tracking_code,
        dt.document_name,
        dr.purpose,
        dr.quantity,
        dr.status,
        dr.remarks,
        dr.request_date
    FROM document_requests dr
    JOIN document_types dt 
        ON dr.document_type_id = dt.document_type_id
    WHERE dr.user_id = ?
    ORDER BY dr.request_date DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$requests = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Requests</title>
</head>
<body>

<h2>My Document Requests</h2>

<?php if (isset($_GET['success'])) { ?>
    <p style="color:green;">Request submitted successfully!</p>
<?php } ?>

<a href="dashboard.php">
    <button>Back to Dashboard</button>
</a>

<br><br>

<?php if ($requests->num_rows > 0) { ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Request ID</th>
        <th>Tracking Code</th>
        <th>Document</th>
        <th>Purpose</th>
        <th>Quantity</th>
        <th>Status</th>
        <th>Remarks</th>
        <th>Request Date</th>
    </tr>

    <?php while ($row = $requests->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['request_id'] ?></td>
        <td><?= htmlspecialchars($row['tracking_code']) ?></td>
        <td><?= htmlspecialchars($row['document_name']) ?></td>
        <td><?= htmlspecialchars($row['purpose']) ?></td>
        <td><?= $row['quantity'] ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td><?= htmlspecialchars($row['remarks']) ?></td>
        <td>
            <?= date("M d, Y h:i A", strtotime($row['request_date'])) ?>
        </td>
    </tr>
    <?php } ?>

</table>

<?php } else { ?>

<p>No document requests found.</p>

<?php } ?>

</body>
</html>