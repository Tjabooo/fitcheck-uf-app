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

require_once 'includes/db_config.php';
require_once 'includes/login_functions.php';
require_once 'includes/validation_functions.php';
require_once 'includes/functions.php';

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email and password
    $email = validate_email_login();
    $password = validate_password();

    if (empty($email_err) && empty($password_err)) {
        $result = login_user($mysqli, $email, $password);
        $email_err = $result['email_err'];
        $password_err = $result['password_err'];
    }

    $mysqli->close();
}

render_template("login_template");

?>