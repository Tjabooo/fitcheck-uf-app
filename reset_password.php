<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/functions.php';

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$invalid_request_err = "";

if (empty($token) || empty($email)) {
    $invalid_request_err = "Invalid request.";
} else {
    $password = $confirm_password = "";
    $password_err = $confirm_password_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $token = $_POST['token'];
        $email = $_POST['email'];

        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a new password.";
        } elseif (strlen(trim($_POST["password"])) < 8) {
            $password_err = "Password must have at least 8 characters.";
        } else {
            $password = trim($_POST["password"]);
        }

        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm your password.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if ($password != $confirm_password) {
                $confirm_password_err = "Passwords do not match.";
            }
        }

        if (empty($password_err) && empty($confirm_password_err)) {
            $invalid_request_err = reset_user_password($mysqli, $email, $token, $password);
        }
        $mysqli->close();
    }
}

include 'templates/reset_password_template.php';
?>