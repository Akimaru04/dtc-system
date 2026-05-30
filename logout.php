<?php
session_start();

/*
|--------------------------------------------------------------------------
| DEBUG (optional, remove later)
|--------------------------------------------------------------------------
*/
echo "<pre>Before:\n";
print_r($_SESSION);

/*
|--------------------------------------------------------------------------
| CLEAR SESSION PROPERLY
|--------------------------------------------------------------------------
*/
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

echo "\nAfter destroy:\n";
var_dump($_SESSION);

echo "\nSession destroyed. Redirecting to login...";

/*
|--------------------------------------------------------------------------
| REDIRECT TO LOGIN
|--------------------------------------------------------------------------
*/
header("Location: index.php");
exit();