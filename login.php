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

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db_config.php';
require_once 'includes/functions.php';

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email_input = $_POST["email"] ?? '';
    if (empty(trim($email_input))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($email_input);
    }

    $password_input = $_POST["password"] ?? '';
    if (empty(trim($password_input))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($password_input);
    }

    if (empty($email_err) && empty($password_err)) {
        $result = login_user($mysqli, $email, $password);
        $email_err = $result['email_err'];
        $password_err = $result['password_err'];
    }

    $mysqli->close();
}

include 'templates/login_template.php';
?>