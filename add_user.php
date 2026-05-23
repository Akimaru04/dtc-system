<?php
session_start();
include(__DIR__ . '/../config/connect.php');
include(__DIR__ . '/../config/auth.php');

checkAuth();
requireRole('admin');

$message = "";

if (isset($_POST['add_member'])) {

    $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year_level = mysqli_real_escape_string($conn, $_POST['year_level']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // IMPORTANT: hash password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users
    (student_number, first_name, last_name, middle_name, email, password, role, course, year_level, contact_number, account_status)
    VALUES
    ('$student_number', '$first_name', '$last_name', '$middle_name', '$email', '$password', '$role', '$course', '$year_level', '$contact_number', 'active')";

    if (mysqli_query($conn, $query)) {
        $message = "Member added successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Member</title>
</head>
<body>

<h2>Add Member (Admin)</h2>

<?php if ($message != "") echo "<p>$message</p>"; ?>

<form method="POST">

    <input type="text" name="student_number" placeholder="Student/Staff ID" required><br><br>

    <input type="text" name="first_name" placeholder="First Name" required><br><br>

    <input type="text" name="last_name" placeholder="Last Name" required><br><br>

    <input type="text" name="middle_name" placeholder="Middle Name"><br><br>

    <input type="email" name="email" placeholder="Email" required><br><br>

    <input type="password" name="password" placeholder="Password" required><br><br>

    <input type="text" name="course" placeholder="Course (or N/A)" required><br><br>

    <input type="text" name="year_level" placeholder="Year Level (or N/A)" required><br><br>

    <input type="text" name="contact_number" placeholder="Contact Number" required><br><br>

    <select name="role" required>
        <option value="student">Student</option>
        <option value="registrar">Registrar</option>
        <option value="admin">Admin</option>
    </select>

    <br><br>

    <button type="submit" name="add_member">Add Member</button>

</form>

</body>
</html>