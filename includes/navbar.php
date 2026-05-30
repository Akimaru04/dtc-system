<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . '/../middleware/auth.php');

$user = auth_user(); // always fetch fresh user safely

$name = $user['name'] ?? 'User';
$role = $user['role'] ?? 'Guest';

$flash = get_flash();
?>

<!-- FLASH MESSAGE (GLOBAL UI FEEDBACK) -->
<?php if ($flash): ?>
    <div class="alert <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<div class="navbar-custom">

    <!-- LEFT -->
    <div class="nav-left">
        <b>DTC System</b>
    </div>

    <!-- RIGHT -->
    <div class="nav-right">

        <span class="user-info">
            Welcome, <?= htmlspecialchars($name) ?>
            (<?= htmlspecialchars(ucfirst($role)) ?>)
        </span>

        <a href="<?= BASE_URL ?>logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</div>