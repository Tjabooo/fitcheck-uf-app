<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/verification_functions.php';
require_once 'includes/functions.php';

$token = $_GET['token'] ?? '';
$verification_err = "";

// Check if token is set
if (empty($token)) {
    $verification_err = "Invalid or missing token.";
} else {
    $verification_err = verify_email_token($mysqli, $token);
}

$mysqli->close();

render_template("verify_email_template");
?>