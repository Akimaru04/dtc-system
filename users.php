<?php
require_once '../auth.php';
checkAuth();
requireRole('admin');

include('../config/connect.php');
include('../includes/navbar.php');

$result = mysqli_query($conn, "SELECT * FROM users");
?>

<h2>User Management</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Student Number</th>
        <th>Name</th>
        <th>Role</th>
        <th>Action</th>
    </tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['user_id']; ?></td>
        <td><?php echo $row['student_number']; ?></td>
        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
        <td><?php echo $row['role']; ?></td>
        <td>
            <a href="delete_user.php?id=<?php echo $row['user_id']; ?>">Delete</a>
        </td>
    </tr>
<?php } ?>

</table>