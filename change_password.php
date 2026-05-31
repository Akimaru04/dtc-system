<?php
session_start();

require_once(__DIR__ . '/config/Database.php');
require_once(__DIR__ . '/middleware/auth.php');

$conn = Database::getInstance()->conn;

// 🔐 Require login
$user = auth_required();

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/
$_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));

$message = "";

/*
|--------------------------------------------------------------------------
| UPDATE PASSWORD
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_password'])) {

    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // --------------------------
    // CSRF CHECK
    // --------------------------
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $message = "Invalid request.";
    }

    // --------------------------
    // VALIDATION
    // --------------------------
    elseif ($new_password !== $confirm_password) {
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

        if ($stmt->execute()) {

            $stmt->close();

            $_SESSION['must_change_password'] = 0;

            // 🔐 Get role from session (no extra query needed)
            $role = $_SESSION['role'];

            switch ($role) {
                case 'admin':
                    header("Location: /admin/dashboard.php");
                    break;

                case 'student':
                    header("Location: /student/dashboard.php");
                    break;

                case 'registrar':
                    header("Location: /registrar/dashboard.php");
                    break;

                default:
                    header("Location: /index.php");
            }

            exit();

        } else {
            $message = "Failed to update password. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>

<body>

<div class="container" style="max-width: 400px; margin-top: 80px;">

    <div class="card">

        <h2 style="text-align:center;">Change Password</h2>

        <p style="text-align:center;">
            You are required to change your password before continuing.
        </p>

        <?php if (!empty($message)): ?>
            <p style="color:red; text-align:center;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <label>New Password</label>
            <input type="password" name="new_password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" name="update_password" class="btn btn-primary" style="width:100%;">
                Update Password
            </button>

        </form>

    </div>

</div>

</body>
</html>