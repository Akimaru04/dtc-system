<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . '/../middleware/auth.php');

$user = auth_user();

$name = $user['name'] ?? 'User';
$role = $user['role'] ?? 'Guest';

$flash = get_flash();
?>

<!-- FLASH MESSAGE -->
<?php if ($flash): ?>
    <div class="alert <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<div class="navbar-custom">

    <div class="nav-left">
        <b>DTC System</b>
    </div>

    <div class="nav-right">

        <span class="user-info">
            Welcome, <?= htmlspecialchars($name) ?>
            (<?= htmlspecialchars(ucfirst($role)) ?>)
        </span>

        <a href="../logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</div>