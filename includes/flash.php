<?php
/*
|--------------------------------------------------------------------------
| FLASH MESSAGE SYSTEM
|--------------------------------------------------------------------------
| Simple session-based flash messages (show once, then auto-remove)
|--------------------------------------------------------------------------
*/

function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash() {

    if (!empty($_SESSION['flash'])) {

        $flash = $_SESSION['flash'];

        unset($_SESSION['flash']); // auto-clear after display

        return $flash;
    }

    return null;
}

/*
|--------------------------------------------------------------------------
| FLASH DISPLAY (CALL THIS IN UI)
|--------------------------------------------------------------------------
*/

function display_flash() {

    $flash = get_flash();

    if (!empty($flash) && is_array($flash)) {

        $type = $flash['type'] ?? 'info';
        $message = $flash['message'] ?? '';

        echo '<div class="alert ' . htmlspecialchars($type) . '">'
            . htmlspecialchars($message) .
        '</div>';
    }
}