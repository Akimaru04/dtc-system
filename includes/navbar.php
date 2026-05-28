<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// fallback safety (prevents crashes if misused)
$name = $user['name'] ?? 'User';
$role = $user['role'] ?? 'Guest';
?>

<div class="navbar-custom">
    <div><b>DTC System</b></div>

    <div>
        Welcome, <?= htmlspecialchars($name) ?>
        (<?= htmlspecialchars(ucfirst($role)) ?>)
        <a href="/logout.php">Logout</a>
    </div>
</div>