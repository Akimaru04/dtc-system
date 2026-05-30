<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . '/config/connect.php');

$error = "";

/*
|--------------------------------------------------------------------------
| ROLE REDIRECT MAP (CLEANER STRUCTURE)
|--------------------------------------------------------------------------
*/
function redirect_by_role($role) {

    $map = [
        'student' => '/student/dashboard.php',
        'registrar' => '/registrar/dashboard.php',
        'admin' => '/admin/dashboard.php',
    ];

    if (isset($map[$role])) {
        header("Location: " . $map[$role]);
    } else {
        session_destroy();
        header("Location: index.php");
    }

    exit();
}

/*
|--------------------------------------------------------------------------
| REDIRECT IF ALREADY LOGGED IN
|--------------------------------------------------------------------------
*/
if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {
    redirect_by_role($_SESSION['role']);
}

/*
|--------------------------------------------------------------------------
| LOGIN PROCESS
|--------------------------------------------------------------------------
*/
if (isset($_POST['login'])) {

    $student_number = trim($_POST['student_number'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("
        SELECT user_id, student_number, password, role, first_name, last_name, must_change_password, account_status
        FROM users
        WHERE student_number = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {

        $user = $result->fetch_assoc();

        /*
        |--------------------------------------------------------------------------
        | ACCOUNT STATUS CHECK (IMPORTANT SECURITY FIX)
        |--------------------------------------------------------------------------
        */
        if ($user['account_status'] !== 'active') {
            $error = "Account is disabled.";
        }

        /*
        |--------------------------------------------------------------------------
        | PASSWORD CHECK
        |--------------------------------------------------------------------------
        */
        elseif (password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['must_change_password'] = (int)$user['must_change_password'];

            /*
            |--------------------------------------------------------------------------
            | FORCE PASSWORD CHANGE
            |--------------------------------------------------------------------------
            */
            if ($_SESSION['must_change_password'] === 1) {
                header("Location: /change_password.php");
                exit();
            }

            redirect_by_role($user['role']);

        } else {
            $error = "Incorrect password.";
        }

    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DTC Login</title>
    <link rel="stylesheet" href="../assets/css/global.css">
</head>

<body>

<div class="container" style="max-width: 400px; margin-top: 80px;">

    <div class="card">

        <h2 style="text-align:center; margin-bottom: 20px;">
            DTC Login
        </h2>

        <form method="POST">

            <label>Student Number</label>
            <input type="text" name="student_number" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" name="login" class="btn btn-primary" style="width:100%;">
                Login
            </button>

        </form>

        <?php if (!empty($error)): ?>
            <p style="color:red; margin-top:10px; text-align:center;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

    </div>

</div>

</body>
</html>