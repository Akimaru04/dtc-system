<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$name = $_SESSION['name'] ?? "";
$role = $_SESSION['role'] ?? "";
?>

<div class="navbar-custom">
    <div><b>DTC System</b></div>

    <div>
        Welcome, <?php echo htmlspecialchars($name); ?>
        (<?php echo htmlspecialchars(ucfirst($role)); ?>)
        <a href="/dtc_system/logout.php">Logout</a>
    </div>
</div>