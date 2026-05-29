<?php
session_start();

include("../config/connect.php");
include("../middleware/auth.php");
include("../config/request_statuses.php");

$user = require_role(['registrar']);

/*
|--------------------------------------------------------------------------
| HANDLE STATUS UPDATE (FULL ADMIN CONTROL)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $request_id = intval($_POST['request_id'] ?? 0);
    $new_status = $_POST['status'] ?? '';

    if ($request_id > 0 && in_array($new_status, $REQUEST_STATUSES)) {

        $stmt = $conn->prepare("
            UPDATE document_requests
            SET status = ?
            WHERE request_id = ?
        ");

        $stmt->bind_param("si", $new_status, $request_id);
        $stmt->execute();
    }

    header("Location: registrar_dashboard.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/
$status_filter = $_GET['status'] ?? '';

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
    $sql .= " WHERE dr.status = ?";
}

$sql .= " ORDER BY dr.request_date DESC";

$stmt = $conn->prepare($sql);

if (!empty($status_filter)) {
    $stmt->bind_param("s", $status_filter);
}

$stmt->execute();
$result = $stmt->get_result();

/*
|--------------------------------------------------------------------------
| STATUS COUNTS
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

<a href="../logout.php"><button>Logout</button></a>

<h1>Registrar Dashboard</h1>

<p>Welcome, <?= htmlspecialchars($user['name']) ?>!</p>

<!-- STATUS OVERVIEW -->
<div style="display:flex; gap:10px; flex-wrap:wrap;">
    <div>Pending Payment: <b><?= $counts['pending_payment'] ?></b></div>
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
            <option value="<?= $s ?>" <?= $status_filter === $s ? 'selected' : '' ?>>
                <?= ucwords(str_replace('_', ' ', $s)) ?>
            </option>
        <?php } ?>
    </select>
    <button type="submit">Filter</button>
</form>

<br>

<!-- TABLE -->
<table border="1" cellpadding="10" cellspacing="0" width="100%">

<tr style="background:#f2f2f2;">
    <th>ID</th>
    <th>Tracking</th>
    <th>Student</th>
    <th>Document</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>

<tr>

    <td><?= $row['request_id'] ?></td>

    <td><b><?= htmlspecialchars($row['tracking_code']) ?></b></td>

    <td>
        <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?><br>
        <small><?= htmlspecialchars($row['student_number']) ?></small>
    </td>

    <td><?= htmlspecialchars($row['document_name']) ?></td>

    <td>
        <b><?= ucwords(str_replace('_', ' ', $row['status'])) ?></b>
    </td>

    <td>
        <?= date("M d, Y h:i A", strtotime($row['request_date'])) ?>
    </td>

    <td>

        <form method="POST" style="display:flex; gap:5px; align-items:center;">

            <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">

            <select name="status" required>

                <option value="" disabled>Select status</option>

                <?php foreach ($REQUEST_STATUSES as $s) { ?>
                    <option value="<?= $s ?>" <?= $row['status'] === $s ? 'selected' : '' ?>>
                        <?= ucwords(str_replace('_', ' ', $s)) ?>
                    </option>
                <?php } ?>

            </select>

            <button type="submit">Update</button>

        </form>

    </td>

</tr>

<?php } ?>

</table>

</body>
</html>