<?php
// auth_check.php

// Start session with cookie parameters
session_set_cookie_params([
    'lifetime' => 86400 * 30,
    'path' => '/',
    'domain' => 'app.fitcheck.nu',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

require_once 'db_config.php';
require_once 'functions.php';

if (!isMobile()) {
    // If the user is on a desktop, display an error message
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Access Restricted</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Outfit" rel="stylesheet">
        <style>
            body {
                text-align: center;
                padding: 50px;
                font-family: "Outfit", sans-serif;
            }
            h1 {
                color: red;
            }
        </style>
    </head>
    <body>
        <h1>Access Restricted</h1>
        <p>This site is only accessible on mobile devices. Please visit using a mobile device.</p>
    </body>
    </html>';
    exit;
}

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if (!isset($_SESSION["username"])) {
    header("location: logout.php");
    exit;
}

$username = $_SESSION["username"];

$sql = "SELECT id FROM users WHERE username = ? LIMIT 1";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows !== 1) {
        $stmt->close();
        header("location: logout.php");
        exit;
    }
    
    $stmt->close();
} else {
    header("location: logout.php");
    exit;
}

?>