<?php
$flash = get_flash();

if ($flash):
    $type = $flash['type'];
    $message = $flash['message'];
?>

<div class="alert <?= htmlspecialchars($type) ?>">
    <?= htmlspecialchars($message) ?>
</div>

<?php endif; ?>