<?php
session_start();

include('../config/connect.php');
include('../config/auth.php');

checkAuth();
requireRole('admin');

$message = "";

/* --------------------------
   ADD DOCUMENT TYPE
-------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $document_name = trim($_POST['document_name'] ?? '');
    $description   = trim($_POST['description'] ?? '');

    if (!$document_name) {
        $message = "Document name is required.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO document_types (document_name, description)
            VALUES (?, ?)
        ");

        $stmt->bind_param("ss", $document_name, $description);

        if ($stmt->execute()) {
            header("Location: document_types.php?success=Document type added successfully");
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
</head>
<body>

<!-- ONLY LOGOUT (NO NAVBAR) -->
<div style="text-align:right;">
    <a href="../logout.php">Logout</a>
</div>

<h2>Add Document Type</h2>

<a href="document_types.php">
    <button type="button">Back</button>
</a>

<br><br>

<?php if (!empty($message)): ?>
    <p style="color:red;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<form method="POST">

    <input type="text" name="document_name" placeholder="Document Name" required>
    <br><br>

    <textarea name="description" placeholder="Description"></textarea>
    <br><br>

    <button type="submit">Add Document</button>

</form>

</body>
</html>