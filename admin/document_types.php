<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| FLASH SYSTEM
|--------------------------------------------------------------------------
*/
include('../includes/flash.php');

/*
|--------------------------------------------------------------------------
| FETCH DOCUMENT TYPES
|--------------------------------------------------------------------------
*/
$result = mysqli_query(
    $conn,
    "SELECT document_type_id, document_name, description 
     FROM document_types 
     ORDER BY document_type_id DESC"
);

/* SAFE FAILOVER */
if (!$result) {
    set_flash("Failed to load document types.", "error");
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Document Types</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <!-- FLASH -->
    <?php if ($flash = get_flash()): ?>
        <div class="alert <?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="card">
        <h1>Document Types</h1>
        <p>Official list of documents used in the system</p>
    </div>

    <!-- ACTION BAR -->
    <div class="card action-bar">

        <a href="dashboard.php" class="btn btn-secondary">
            Back to Dashboard
        </a>

        <a href="add_document.php" class="btn btn-primary">
            + Add Document Type
        </a>

    </div>

    <!-- TABLE -->
    <div class="card">

        <div class="table-wrapper">

            <table class="reg-table">

                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Description</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="2" class="empty-state">
                                No document types found
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <b><?= htmlspecialchars($row['document_name']) ?></b>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['description']) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>