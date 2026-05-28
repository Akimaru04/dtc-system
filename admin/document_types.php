<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

// 🔐 middleware protection
$user = require_role(['admin']);

// Fetch document types
$result = mysqli_query($conn, "SELECT * FROM document_types ORDER BY document_type_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Document Types</title>
</head>
<body>

<!-- LOGOUT -->
<div style="text-align:right;">
    <a href="../logout.php"><button>Logout</button></a>
</div>

<h2>Manage Document Types</h2>

<!-- ACTION BUTTONS -->
<a href="add_document.php">
    <button type="button">Add Document Type</button>
</a>

<a href="dashboard.php">
    <button type="button">Back to Dashboard</button>
</a>

<br><br>

<table border="1" cellpadding="10" cellspacing="0">

    <tr>
        <th>Document Name</th>
        <th>Description</th>
        <th>Action</th>
    </tr>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <tr>
            <td colspan="4">No document types found</td>
        </tr>
    <?php endif; ?>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= htmlspecialchars($row['document_name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>

            <td>
                <a href="edit_document.php?id=<?= $row['document_type_id'] ?>">Edit</a> |
                <a href="delete_document.php?id=<?= $row['document_type_id'] ?>"
                   onclick="return confirm('Delete this document type?')"
                   style="color:red;">
                   Delete
                </a>
            </td>
        </tr>
    <?php } ?>

</table>

</body>
</html>