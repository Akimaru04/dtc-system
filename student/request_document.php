<?php
session_start();

include("../config/connect.php");
include("../middleware/auth.php");

// 🔐 student-only access
$user = require_role(['student']);

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| FETCH DOCUMENT TYPES (ACTIVE ONLY)
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT document_type_id, document_name 
    FROM document_types 
    WHERE is_active = 1
    ORDER BY document_name ASC
");

$stmt->execute();
$documents = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Document</title>
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<h2>Request Document</h2>

<a href="dashboard.php"><button>Back to Dashboard</button></a>
<br><br>

<form method="POST" action="submit_request.php">

    <label>Document Type</label><br>
    <select name="document_type_id" required>
        <option value="">-- Select Document --</option>

        <?php while ($doc = $documents->fetch_assoc()) { ?>
            <option value="<?= $doc['document_type_id'] ?>">
                <?= htmlspecialchars($doc['document_name']) ?>
            </option>
        <?php } ?>

    </select>

    <br><br>

    <label>Purpose</label><br>
    <input type="text" name="purpose" required>

    <br><br>

    <label>Quantity</label><br>
    <input type="number" name="quantity" value="1" min="1" required>

    <br><br>

    <label>Remarks (optional)</label><br>
    <textarea name="remarks"></textarea>

    <br><br>

    <button type="submit">Submit Request</button>

</form>

</body>
</html><?php
session_start();

include("../config/connect.php");
include("../middleware/auth.php");

// 🔐 student-only access
$user = require_role(['student']);

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| FETCH DOCUMENT TYPES (ACTIVE ONLY)
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT document_type_id, document_name 
    FROM document_types 
    WHERE is_active = 1
    ORDER BY document_name ASC
");

$stmt->execute();
$documents = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Document</title>
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<h2>Request Document</h2>

<a href="dashboard.php"><button>Back to Dashboard</button></a>
<br><br>

<form method="POST" action="submit_request.php">

    <label>Document Type</label><br>
    <select name="document_type_id" required>
        <option value="">-- Select Document --</option>

        <?php while ($doc = $documents->fetch_assoc()) { ?>
            <option value="<?= $doc['document_type_id'] ?>">
                <?= htmlspecialchars($doc['document_name']) ?>
            </option>
        <?php } ?>

    </select>

    <br><br>

    <label>Purpose</label><br>
    <input type="text" name="purpose" required>

    <br><br>

    <label>Quantity</label><br>
    <input type="number" name="quantity" value="1" min="1" required>

    <br><br>

    <label>Remarks (optional)</label><br>
    <textarea name="remarks"></textarea>

    <br><br>

    <button type="submit">Submit Request</button>

</form>

</body>
</html>