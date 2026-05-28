<?php
session_start();

include(__DIR__ . '/config/connect.php');
include(__DIR__ . '/middleware/auth.php');

// 🔐 require login (any role allowed)
$user = auth_required();

$user_id = $_SESSION['user_id'];

$message = "";

/* --------------------------
   UPDATE PASSWORD
-------------------------- */
if (isset($_POST['update_password'])) {

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // --------------------------
    // BASIC VALIDATION
    // --------------------------
    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";

    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters.";

    } else {

        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users
            SET password = ?, must_change_password = 0
            WHERE user_id = ?
        ");

        $stmt->bind_param("si", $hashed, $user_id);
        $stmt->execute();
        $stmt->close();

        // 🔐 update session flag only
        $_SESSION['must_change_password'] = 0;

        // 🔐 GET ROLE FROM DATABASE (NOT SESSION)
        $stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // --------------------------
        // REDIRECT BY ROLE
        // --------------------------
        if ($user['role'] == 'admin') {
            header("Location: /admin/dashboard.php");
        } elseif ($user['role'] == 'student') {
            header("Location: /student/dashboard.php");
        } elseif ($user['role'] == 'registrar') {
            header("Location: /registrar/dashboard.php");
        } else {
            header("Location: /index.php");
        }

        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
</head>
<body>

<h2>Change Your Password</h2>

<p>You are required to change your password before continuing.</p>

<?php if (!empty($message)): ?>
    <p style="color:red;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST">

    <input type="password" name="new_password" placeholder="New Password" required><br><br>

    <input type="password" name="confirm_password" placeholder="Confirm Password" required><br><br>

    <button type="submit" name="update_password">
        Update Password
    </button>

</form>

</body>
</html>