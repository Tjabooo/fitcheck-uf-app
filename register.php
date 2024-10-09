<?php
session_start();

require_once 'includes/db_config.php';
require_once 'includes/registration_functions.php';
require_once 'includes/verification_functions.php';
require_once 'includes/functions.php';

$ip_address = $_SERVER['REMOTE_ADDR'];
$max_attempts = 5;
$attempt_window = 3600;

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";
$rate_limit_err = "";
$message = "";
$can_resend = true;
$remaining_time = 0;

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['resend_verification'])) {
        handle_resend_verification($mysqli);
    } else {
        handle_registration($mysqli, $ip_address, $max_attempts, $attempt_window);
    }
}

$mysqli->close();

render_template("register_template");
?>