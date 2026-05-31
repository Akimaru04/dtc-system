<?php

/*
|--------------------------------------------------------------------------
| CLEAN INPUT (FOR GENERAL USE / OUTPUT ESCAPING)
|--------------------------------------------------------------------------
*/
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| ESCAPE FOR LIKE QUERY (SQL SAFE)
|--------------------------------------------------------------------------
*/
function escape_like($conn, $data) {

    $data = trim($data);

    // Escape SQL LIKE special characters
    $data = str_replace(
        ['%', '_'],
        ['\\%', '\\_'],
        $data
    );

    return "%" . $conn->real_escape_string($data) . "%";
}