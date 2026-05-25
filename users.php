<?php
// Start session
session_start();

// Include required files
include('../config/connect.php');
include('../config/auth.php');
include('../includes/navbar.php');

// Auth checks
checkAuth();
requireRole('admin');

// Fetch users (latest first)
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
</head>
<body>

<h2>User Management</h2>

<!-- ACTION BUTTONS -->
<a href="add_user.php"><button>Add New User</button></a>
<a href="dashboard.php"><button>Back to Dashboard</button></a>

<br><br>

<!-- SUCCESS MESSAGE -->
<?php if (isset($_GET['success'])): ?>
    <p style="color:green;">
        <?= htmlspecialchars($_GET['success']) ?>
    </p>
<?php endif; ?>

<!-- USERS TABLE -->
<table border="1" cellpadding="10" cellspacing="0">

    <tr>
        <th>ID</th>
        <th>Student Number</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Course</th>
        <th>Year Level</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <!-- NO USERS -->
    <?php if (mysqli_num_rows($result) == 0): ?>
        <tr>
            <td colspan="9">No users found</td>
        </tr>
    <?php endif; ?>

    <!-- LOOP USERS -->
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>

            <td><?= $row['user_id'] ?></td>
            <td><?= $row['student_number'] ?></td>
            <td><?= $row['first_name'] . " " . $row['last_name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= ucfirst($row['role']) ?></td>
            <td><?= $row['course'] ?></td>
            <td><?= $row['year_level'] ?></td>

            <td>
                <?php if ($row['account_status'] == 'active') { ?>
                    <span style="color:green;">Active</span>
                <?php } else { ?>
                    <span style="color:red;">Inactive</span>
                <?php } ?>
            </td>

            <td>
                <!-- EDIT USER -->
                <a href="edit_user.php?id=<?= $row['user_id'] ?>">Edit</a>

                |

                <!-- RESET PASSWORD -->
                <a href="reset_password.php?id=<?= $row['user_id'] ?>"
                   onclick="return confirm('Reset this user password?')"
                   style="color:orange;">
                   Reset
                </a>

                |

                <!-- DELETE USER -->
                <a href="delete_user.php?id=<?= $row['user_id'] ?>"
                   onclick="return confirm('Are you sure?')"
                   style="color:red;">
                   Delete
                </a>
            </td>

        </tr>
    <?php } ?>

</table>

</body>
</html>