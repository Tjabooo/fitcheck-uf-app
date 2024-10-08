<?php
session_start();

require_once 'includes/db_config.php';
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['resend_verification'])) {
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        $resend_result = resend_verification_email($mysqli, $email);
        $email_err = $resend_result['email_err'];
        $message = $resend_result['message'];
        $can_resend = $resend_result['can_resend'];
        $remaining_time = $resend_result['remaining_time'];

        include 'templates/registration_confirmation_template.php';
        exit;
    } else {
        $attempt_count = check_registration_attempts($mysqli, $ip_address, $max_attempts, $attempt_window);
        if ($attempt_count >= $max_attempts) {
            $rate_limit_err = "Too many registration attempts. Please try again later.";
            sleep(1);
        } else {
            record_registration_attempt($mysqli, $ip_address);

            if (empty(trim($_POST["username"]))) {
                $username_err = "Please enter a username.";
            } elseif (!preg_match('/^[a-zA-Z0-9]+$/', trim($_POST["username"]))) {
                $username_err = "Username can only contain letters and numbers.";
            } else {
                $username = trim($_POST["username"]);
                $sql = "SELECT id FROM users WHERE username = ?";
                if ($stmt = $mysqli->prepare($sql)) {
                    $stmt->bind_param("s", $username);
                    if ($stmt->execute()) {
                        $stmt->store_result();
                        if ($stmt->num_rows == 1) {
                            $username_err = "This username is already taken.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    $stmt->close();
                }
            }

            if (empty(trim($_POST["email"]))) {
                $email_err = "Please enter an email.";
            } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
                $email_err = "Please enter a valid email address.";
            } else {
                $email = trim($_POST["email"]);
                $sql = "SELECT id FROM users WHERE email = ?";
                if ($stmt = $mysqli->prepare($sql)) {
                    $stmt->bind_param("s", $email);
                    if ($stmt->execute()) {
                        $stmt->store_result();
                        if ($stmt->num_rows == 1) {
                            $email_err = "This email is already registered.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    $stmt->close();
                }
            }

            if (empty(trim($_POST["password"]))) {
                $password_err = "Please enter a password.";
            } elseif (strlen(trim($_POST["password"])) < 8) {
                $password_err = "Password must have at least 8 characters.";
            } else {
                $password = trim($_POST["password"]);
            }

            if (empty(trim($_POST["confirm_password"]))) {
                $confirm_password_err = "Please confirm password.";
            } else {
                $confirm_password = trim($_POST["confirm_password"]);
                if (empty($password_err) && ($password != $confirm_password)) {
                    $confirm_password_err = "Passwords did not match.";
                }
            }

            if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
                $mysqli->begin_transaction();

                try {
                    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                    if ($stmt = $mysqli->prepare($sql)) {
                        $param_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt->bind_param("sss", $username, $email, $param_password);
                        $stmt->execute();
                        $user_id = $stmt->insert_id;
                        $stmt->close();
                    } else {
                        throw new Exception("User insertion failed.");
                    }

                    $token = bin2hex(random_bytes(32));
                    $expires = date("Y-m-d H:i:s", time() + 86400);
                    $current_time = date("Y-m-d H:i:s", time());

                    $sql = "INSERT INTO email_verification_tokens (user_id, token, expires, last_sent) VALUES (?, ?, ?, ?)";
                    if ($stmt = $mysqli->prepare($sql)) {
                        $stmt->bind_param("isss", $user_id, $token, $expires, $current_time);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        throw new Exception("Token insertion failed.");
                    }

                    $mysqli->commit();

                    send_verification_email($email, $token);

                    $_SESSION['email'] = $email;

                    $can_resend = true;
                    $remaining_time = 0;
                    $message = '';
                    $email_err = '';

                    include 'templates/registration_confirmation_template.php';
                    exit;
                } catch (Exception $e) {
                    $mysqli->rollback();
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
}

$mysqli->close();

include 'templates/register_template.php';
?>