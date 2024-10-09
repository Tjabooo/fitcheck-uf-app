<?php

// Start cookie session
session_set_cookie_params([
    'lifetime' => 86400 * 30,
    'path' => '/',
    'domain' => 'app.fitcheck.nu',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Redirect user to login.php (templates/login_template.php) if user is not logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

?>