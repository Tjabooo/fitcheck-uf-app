<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/pass_reset_functions.php';
require_once 'includes/functions.php';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$invalid_request_err = "";

// Check if token and email are set
if (empty($token) || empty($email)) {
    $invalid_request_err = "Invalid request.";
    render_template("reset_password_template");
    exit;
}

$password = $confirm_password = "";
$password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    handle_password_reset($mysqli);
}

$mysqli->close();
render_template("reset_password_template");
?>