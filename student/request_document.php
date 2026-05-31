<?php
session_start();
require_once("../config/Database.php");
$conn = Database::getInstance()->conn;
include('../middleware/auth.php');

$user = require_role(['student']);
enforce_password_change($user);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Document</title>

    <link rel="stylesheet" href="../assets/css/global.css">
</head>

<body>

<?php include('../includes/navbar.php'); ?>

<div class="container">

    <div class="card">

    <h1>Request Document</h1>
    <p>Fill out the form below to submit your document request.</p>

    <div style="margin-top: 10px;">
        <a href="dashboard.php">
            <button type="button" class="btn btn-secondary">
                ← Back to Dashboard
            </button>
        </a>
    </div>

</div>

    <!-- FORM -->
    <div class="card">

        <form method="POST" action="process_request.php">

            <label>Document Type</label>
            <select name="document_type_id" required>
                <option value="">-- Select Document --</option>
                <?php
                $docs = $conn->query("SELECT document_type_id, document_name FROM document_types");
                while ($doc = $docs->fetch_assoc()) {
                    echo "<option value='{$doc['document_type_id']}'>
                            {$doc['document_name']}
                        </option>";
                }
                ?>
            </select>

            <label>Purpose</label>
            <textarea name="purpose" rows="4" required></textarea>

            <label>Contact Number</label>
            <input type="text" name="contact_number" required>

            <button type="submit" class="btn" style="background:#0d6efd; color:white; width:100%;">
                Submit Request
            </button>

        </form>

    </div>

</div>

</body>
</html>