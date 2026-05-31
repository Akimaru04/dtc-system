<?php
session_start();

require_once("../config/Database.php");
$conn = Database::getInstance()->conn;

require_once('../middleware/auth.php');
require_once('../includes/csrf.php');
require_once('../includes/flash.php'); // ✅ ADD THIS

$user = require_role(['admin']);
enforce_password_change($user);

/*
|--------------------------------------------------------------------------
| POST HANDLER
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $document_name = trim($_POST['document_name'] ?? '');
    $description   = trim($_POST['description'] ?? '');

    if ($document_name === '') {

        set_flash("error", "Document name is required.");
        header("Location: add_document.php");
        exit();

    } else {

        /*
        |--------------------------------------------------------------------------
        | DUPLICATE CHECK
        |--------------------------------------------------------------------------
        */
        $check = $conn->prepare("
            SELECT id FROM document_types 
            WHERE document_name = ? 
            LIMIT 1
        ");

        $check->bind_param("s", $document_name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            set_flash("error", "Document already exists.");
            header("Location: add_document.php");
            exit();

        } else {

            $stmt = $conn->prepare("
                INSERT INTO document_types (document_name, description)
                VALUES (?, ?)
            ");

            $stmt->bind_param("ss", $document_name, $description);

            if ($stmt->execute()) {

                set_flash("success", "Document added successfully.");
                header("Location: document_types.php");
                exit();

            } else {

                set_flash("error", "Database error: " . $stmt->error);
                header("Location: add_document.php");
                exit();
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Document Type</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <!-- HEADER -->
    <div class="card admin-header">
        <h1>Add Document Type</h1>
        <p>Create official requestable documents</p>
    </div>

    <!-- BACK BUTTON -->
    <div class="card action-bar">
        <a href="document_types.php" class="btn btn-secondary">
            ← Back
        </a>
    </div>

    <!-- MESSAGE -->
    <?php if (!empty($message)) { ?>
        <div class="card">
            <p><b><?= htmlspecialchars($message) ?></b></p>
        </div>
    <?php } ?>

    <!-- FORM -->
    <div class="card">

        <form method="POST" class="form-grid">

            <?= csrf_field() ?>

            <input type="text"
                name="document_name"
                placeholder="Document Name"
                required>

            <input type="text"
                name="description"
                placeholder="Description (optional)">

            <button type="submit" class="btn btn-primary">
                Add Document
            </button>

        </form>

    </div>

</div>

</body>
</html>