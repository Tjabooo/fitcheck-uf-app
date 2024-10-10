<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/pass_reset_functions.php';
require_once 'includes/functions.php';

blockDesktopAccess();

$email = $email_err = "";

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty($email_err)) {
        $email_err = handle_password_reset_request($mysqli, $email);
    }

    $mysqli->close();
}

render_template("request_pass_reset_template");
?>