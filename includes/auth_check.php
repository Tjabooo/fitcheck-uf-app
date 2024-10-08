<?php
session_set_cookie_params([
    'lifetime' => 86400 * 30,
    'path' => '/',
    'domain' => 'app.fitcheck.nu',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>