<?php
session_start();

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;
include("../middleware/auth.php");

// student-only access
require_role(['student']);

$user_id = $_SESSION['user_id'];

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

    <link rel="stylesheet" href="../assets/css/global.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <!-- HEADER -->
    <div class="card">
        <h2>My Document Requests</h2>

        <?php if (isset($_GET['success'])) { ?>
            <p style="color:green; margin-top:10px;">
                Request submitted successfully!
            </p>
        <?php } ?>

        <div style="margin-top:10px;">
            <a href="dashboard.php">
                <button class="btn" style="background:#6c757d; color:white;">
                    ← Back to Dashboard
                </button>
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card">

        <?php if ($requests->num_rows > 0) { ?>

            <div class="table-wrapper">
                <table>

                    <thead>
                        <tr>
                            <th>Tracking Code</th>
                            <th>Document</th>
                            <th>Purpose</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $requests->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tracking_code']) ?></td>
                            <td><?= htmlspecialchars($row['document_name']) ?></td>
                            <td><?= htmlspecialchars($row['purpose']) ?></td>
                            <td><?= $row['quantity'] ?></td>

                            <td>
                                <span class="badge <?= htmlspecialchars($row['status']) ?>">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $row['status']))) ?>
                                </span>
                            </td>

                            <td>
                                <?= date("M d, Y h:i A", strtotime($row['request_date'])) ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>

        <?php } else { ?>

            <p>No document requests found.</p>

        <?php } ?>

    </div>

</div>

</body>
</html>