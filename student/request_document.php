<?php
include("../config/connect.php");
include("../config/auth.php");

checkAuth(); // IMPORTANT: protect page

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Document</title>
</head>
<body>

<?php include("../includes/navbar.php"); ?>

<h2>Request Document</h2>

<form method="POST" action="submit_request.php">

    <label>Document Type</label><br>
    <select name="document_type" required>
        <option value="Transcript of Records">Transcript of Records</option>
        <option value="Certificate of Enrollment">Certificate of Enrollment</option>
        <option value="Good Moral Certificate">Good Moral Certificate</option>
        <option value="Diploma">Diploma</option>
        <option value="Certificate of Grades">Certificate of Grades</option>
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