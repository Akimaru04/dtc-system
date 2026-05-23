<?php
include("../config/dbconnect.php");
include("../config/auth.php");

requireRole("registrar");

/*
|--------------------------------------------------------------------------
| FETCH ALL REQUESTS
|--------------------------------------------------------------------------
*/
$sql = "SELECT dr.*, u.first_name, u.last_name, u.student_number
        FROM document_requests dr
        JOIN users u ON dr.user_id = u.user_id
        ORDER BY dr.request_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php include("../includes/navbar.php"); ?>

<div class="container-custom">

    <h2>Registrar Dashboard</h2>
    <p>Manage and process document requests</p>

    <table class="table-custom">

        <tr>
            <th>Request ID</th>
            <th>Student</th>
            <th>Student No.</th>
            <th>Document</th>
            <th>Status</th>
            <th>Update</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <tr>
            <td><?php echo $row['request_id']; ?></td>

            <td>
                <?php echo $row['first_name'] . " " . $row['last_name']; ?>
            </td>

            <td>
                <?php echo $row['student_number']; ?>
            </td>

            <td>
                <?php echo $row['document_type']; ?>
            </td>

            <td>
                <?php echo $row['request_status']; ?>
            </td>

            <td>

                <form method="POST" action="update_request_status.php">

                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">

                    <select name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Ready for Claiming">Ready for Claiming</option>
                        <option value="Claimed">Claimed</option>
                        <option value="Rejected">Rejected</option>
                    </select>

                    <button type="submit" class="btn-custom btn-primary-custom">
                        Update
                    </button>

                </form>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>