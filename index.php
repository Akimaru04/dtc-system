<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . '/config/connect.php');

$error = "";

/*
|--------------------------------------------------------------------------
| IF USER IS ALREADY LOGGED IN → REDIRECT TO DASHBOARD
|--------------------------------------------------------------------------
*/
if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {

    switch ($_SESSION['role']) {

        case 'student':
            header("Location: /student/dashboard.php");
            exit();

        case 'registrar':
            header("Location: /registrar/dashboard.php");
            exit();

        case 'admin':
            header("Location: /admin/dashboard.php");
            exit();

        default:
            session_unset();
            session_destroy();
            header("Location: index.php");
            exit();
    }
}

/*
|--------------------------------------------------------------------------
| LOGIN PROCESS
|--------------------------------------------------------------------------
*/
if (isset($_POST['login'])) {

    $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE student_number = '$student_number' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            // regenerate session (security)
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['must_change_password'] = $user['must_change_password'];

            // 🔐 FORCE PASSWORD CHANGE CHECK
            if ($user['must_change_password'] == 1) {
                header("Location: /change_password.php");
                exit();
            }

            // ROLE-BASED REDIRECT
            switch ($user['role']) {

                case 'student':
                    header("Location: /student/dashboard.php");
                    exit();

                case 'registrar':
                    header("Location: /registrar/dashboard.php");
                    exit();

                case 'admin':
                default:
                    header("Location: /admin/dashboard.php");
                    exit();
            }

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
    <input type="text" name="student_number" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

</body>
</html>