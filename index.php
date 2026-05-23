<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . '/config/connect.php');

// redirect if already logged in
if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] === 'student') {
        header("Location: student/dashboard.php");
        exit();
    }
    elseif ($_SESSION['role'] === 'registrar') {
        header("Location: registrar/dashboard.php");
        exit();
    }
    else {
        header("Location: admin/dashboard.php");
        exit();
    }
}

// login logic
$error = "";

if (isset($_POST['login'])) {

    $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE student_number='$student_number' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];

            if ($user['role'] === 'student') {
                header("Location: student/dashboard.php");
            }
            elseif ($user['role'] === 'registrar') {
                header("Location: registrar/dashboard.php");
            }
            else {
                header("Location: admin/dashboard.php");
            }
            exit();

        } else {
            $error = "Wrong password";
        }

    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DTC Login</title>
</head>
<body>

<h2>Login</h2>

<form method="POST">
    <input type="text" name="student_number" placeholder="Student Number" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>

<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

</body>
</html>