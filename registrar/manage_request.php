<?php
session_start();

include("../config/connect.php");
include("../middleware/auth.php");
include("../config/request_statuses.php");

require_role(['registrar']);

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/
$status_filter = $_GET['status'] ?? '';

/*
|--------------------------------------------------------------------------
| BASE QUERY
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT 
        dr.request_id,
        dr.status,
        dr.request_date,
        dr.tracking_code,
        u.first_name,
        u.last_name,
        u.student_number,
        dt.document_name
    FROM document_requests dr
    JOIN users u ON dr.user_id = u.user_id
    JOIN document_types dt ON dr.document_type_id = dt.document_type_id
";

if (!empty($status_filter)) {
    $sql .= " WHERE dr.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

$sql .= " ORDER BY dr.request_date DESC";

$result = mysqli_query($conn, $sql);

/*
|--------------------------------------------------------------------------
| STATUS COUNTS (DASHBOARD)
|--------------------------------------------------------------------------
*/
$counts = [];
foreach ($REQUEST_STATUSES as $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM document_requests WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $counts[$status] = $stmt->get_result()->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Dashboard</title>
</head>
<body>

<a href="dashboard.php"><button>Back</button></a>

<h2>Registrar Dashboard</h2>

<!-- DASHBOARD CARDS -->
<div style="display:flex; gap:10px; flex-wrap:wrap;">
    <div>Pending: <b><?= $counts['pending_payment'] ?></b></div>
    <div>Uploaded: <b><?= $counts['payment_uploaded'] ?></b></div>
    <div>Verified: <b><?= $counts['payment_verified'] ?></b></div>
    <div>Processing: <b><?= $counts['processing'] ?></b></div>
    <div>Ready: <b><?= $counts['ready_for_pickup'] ?></b></div>
</div>

<br>

<!-- FILTER -->
<form method="GET">
    <select name="status">
        <option value="">All</option>
        <?php foreach ($REQUEST_STATUSES as $s) { ?>
            <option value="<?= $s ?>" <?= $status_filter == $s ? 'selected' : '' ?>>
                <?= ucwords(str_replace('_', ' ', $s)) ?>
            </option>
        <?php } ?>
    </select>
    <button>Filter</button>
</form>

<br>

<!-- TABLE -->
<table border="1" cellpadding="10">

<tr>
    <th>ID</th>
    <th>Tracking</th>
    <th>Student</th>
    <th>Document</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>

<tr>
    <td><?= $row['request_id'] ?></td>
    <td><?= htmlspecialchars($row['tracking_code']) ?></td>
    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
    <td><?= htmlspecialchars($row['document_name']) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td><?= date("M d, Y h:i A", strtotime($row['request_date'])) ?></td>

    <td>
        <form method="POST" action="update_request_status.php">
            <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">

            <select name="status" required>
                <?php foreach ($REQUEST_STATUSES as $s) { ?>
                    <option value="<?= $s ?>"><?= ucwords(str_replace('_', ' ', $s)) ?></option>
                <?php } ?>
            </select>

            <button>Update</button>
        </form>
    </td>
</tr>

<?php } ?>

</table>

</body>
</html>