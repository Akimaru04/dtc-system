<?php
session_start();

include('../config/connect.php');
include('../middleware/auth.php');
include('../includes/csrf.php');

$user = require_role(['admin']);
enforce_password_change($user);

$message = "";

/*
|--------------------------------------------------------------------------
| CSRF PROTECTION
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function verify_csrf() {

    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }
}

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
        $message = "Document name is required.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO document_types (document_name, description)
            VALUES (?, ?)
        ");

        $stmt->bind_param("ss", $document_name, $description);

        if ($stmt->execute()) {
            header("Location: document_types.php?success=1");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
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