<?php
session_start();

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;

require_once('../middleware/auth.php');
require_once('../includes/flash.php'); // ✅ FIXED (standardized)

$user = require_role(['admin']);

/*
|--------------------------------------------------------------------------
| FETCH DOCUMENT TYPES
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT document_type_id, document_name, description 
    FROM document_types 
    ORDER BY document_type_id DESC
");

if (!$stmt) {
    set_flash("error", "Failed to prepare query.");
    header("Location: dashboard.php");
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

/*
|--------------------------------------------------------------------------
| SAFETY CHECK
|--------------------------------------------------------------------------
*/
if (!$result) {
    set_flash("error", "Failed to load document types.");
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

    <?php display_flash(); ?> <!-- ✅ CLEAN STANDARD -->
        <div class="alert <?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php ?>

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

                    <?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="2" class="empty-state">
                                No document types found
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php while ($row = $result->fetch_assoc()): ?>
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

<?php $stmt->close(); ?>