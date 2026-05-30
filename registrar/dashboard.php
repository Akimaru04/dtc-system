<?php
session_start();

include("../config/connect.php");
include("../middleware/auth.php");
include("../config/request_statuses.php");

$user = require_role(['registrar']);

/*
|--------------------------------------------------------------------------
| STATUS UPDATE
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
| FILTERS + PAGINATION
|--------------------------------------------------------------------------
*/
$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

/*
|--------------------------------------------------------------------------
| MAIN QUERY
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
    WHERE 1=1
";

$params = [];
$types = "";

if ($status_filter !== '') {
    $sql .= " AND dr.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($search !== '') {

    $sql .= " AND (
        dr.tracking_code LIKE ?
        OR u.first_name LIKE ?
        OR u.last_name LIKE ?
        OR u.student_number LIKE ?
    )";

    $like = "%$search%";

    for ($i = 0; $i < 4; $i++) {
        $params[] = $like;
        $types .= "s";
    }
}

/*
|--------------------------------------------------------------------------
| COUNT QUERY
|--------------------------------------------------------------------------
*/
$count_sql = "
    SELECT COUNT(*) as total
    FROM document_requests dr
    JOIN users u ON dr.user_id = u.user_id
    WHERE 1=1
";

$count_params = [];
$count_types = "";

if ($status_filter !== '') {
    $count_sql .= " AND dr.status = ?";
    $count_params[] = $status_filter;
    $count_types .= "s";
}

if ($search !== '') {

    $count_sql .= " AND (
        dr.tracking_code LIKE ?
        OR u.first_name LIKE ?
        OR u.last_name LIKE ?
        OR u.student_number LIKE ?
    )";

    $like = "%$search%";

    for ($i = 0; $i < 4; $i++) {
        $count_params[] = $like;
        $count_types .= "s";
    }
}

$count_stmt = $conn->prepare($count_sql);

if (!empty($count_params)) {
    $count_stmt->bind_param($count_types, ...$count_params);
}

$count_stmt->execute();
$total_rows = $count_stmt->get_result()->fetch_assoc()['total'];

$total_pages = ceil($total_rows / $limit);

/*
|--------------------------------------------------------------------------
| FINAL QUERY
|--------------------------------------------------------------------------
*/
$sql .= " ORDER BY dr.request_date DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
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

    $stmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM document_requests 
        WHERE status = ?
    ");

    $stmt->bind_param("s", $status);
    $stmt->execute();

    $counts[$status] = $stmt->get_result()->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Dashboard</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/registrar.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <!-- HEADER -->
    <div class="card reg-header">
        <h1>Registrar Dashboard</h1>
        <p>Manage and process document requests efficiently</p>
    </div>

    <!-- STATUS CARDS -->
    <div class="card">
        <h3>Status Overview</h3>

        <div class="grid-3">

            <?php foreach ($counts as $label => $count) { ?>
                <div class="card status-box">
                    <b><?= ucwords(str_replace('_', ' ', $label)) ?></b>
                    <div style="font-size:20px; margin-top:5px;">
                        <?= $count ?>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card filter-bar">

        <form method="GET" class="filter-form">

            <input 
                type="text"
                name="search"
                placeholder="Search tracking code, name, or student no."
                value="<?= htmlspecialchars($search) ?>"
            >

            <select name="status">
                <option value="">All Status</option>

                <?php foreach ($REQUEST_STATUSES as $s) { ?>
                    <option value="<?= $s ?>" <?= $status_filter === $s ? 'selected' : '' ?>>
                        <?= ucwords(str_replace('_', ' ', $s)) ?>
                    </option>
                <?php } ?>

            </select>

            <button class="btn btn-primary">Filter</button>

        </form>

    </div>

    <!-- TABLE -->
    <div class="card">

        <div class="table-wrapper">

            <table class="reg-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tracking</th>
                        <th>Student</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($result->num_rows > 0) { ?>

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
                                <span class="badge <?= $row['status'] ?>">
                                    <?= ucwords(str_replace('_', ' ', $row['status'])) ?>
                                </span>
                            </td>

                            <td>
                                <?= date("M d, Y h:i A", strtotime($row['request_date'])) ?>
                            </td>

                            <td>
                                <form method="POST" class="action-form">
                                    <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">

                                    <select name="status">
                                        <?php foreach ($REQUEST_STATUSES as $s) { ?>
                                            <option value="<?= $s ?>" <?= $row['status'] === $s ? 'selected' : '' ?>>
                                                <?= ucwords(str_replace('_', ' ', $s)) ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                    <button type="submit" class="btn btn-success">
                                        Update
                                    </button>
                                </form>
                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="7" class="empty-state">
                            No requests found
                        </td>
                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- PAGINATION -->
    <div class="pagination">

        <?php if ($page > 1) { ?>
            <a href="?page=<?= $page - 1 ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">
                Prev
            </a>
        <?php } ?>

        <span>Page <?= $page ?> of <?= $total_pages ?></span>

        <?php if ($page < $total_pages) { ?>
            <a href="?page=<?= $page + 1 ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>">
                Next
            </a>
        <?php } ?>

    </div>

</div>

</body>
</html>