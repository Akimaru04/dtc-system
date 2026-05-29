<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . '/config/connect.php');

$error = "";

/*
|--------------------------------------------------------------------------
| REDIRECT IF ALREADY LOGGED IN
|--------------------------------------------------------------------------
*/
if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {

    $role = $_SESSION['role'];

    if ($role === 'student') {
        header("Location: /student/dashboard.php");
        exit();
    } elseif ($role === 'registrar') {
        header("Location: /registrar/dashboard.php");
        exit();
    } elseif ($role === 'admin') {
        header("Location: /admin/dashboard.php");
        exit();
    } else {
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

    $student_number = trim($_POST['student_number']);
    $password = $_POST['password'];

    // ✅ SECURE QUERY (FIXED)
    $stmt = $conn->prepare("
        SELECT user_id, student_number, password, role, first_name, last_name, must_change_password
        FROM users
        WHERE student_number = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // ✅ VERIFY PASSWORD
        if (password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            // ✅ STORE ONLY WHAT YOU NEED
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['must_change_password'] = (int)$user['must_change_password'];

            // 🔐 FORCE PASSWORD CHANGE
            if ($_SESSION['must_change_password'] === 1) {
                header("Location: /change_password.php");
                exit();
            }

            // 🔁 ROLE REDIRECT
            if ($user['role'] === 'student') {
                header("Location: /student/dashboard.php");
            } elseif ($user['role'] === 'registrar') {
                header("Location: /registrar/dashboard.php");
            } elseif ($user['role'] === 'admin') {
                header("Location: /admin/dashboard.php");
            } else {
                session_destroy();
                header("Location: index.php");
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
    <input type="text" name="student_number" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

</body>
</html>