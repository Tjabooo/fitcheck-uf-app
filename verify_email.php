<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/functions.php';

$token = $_GET['token'] ?? '';
$verification_err = "";

if (empty($token)) {
    $verification_err = "Invalid or missing token.";
} else {
    $verification_err = verify_email_token($mysqli, $token);
}

$mysqli->close();

include 'templates/verify_email_template.php';
?>