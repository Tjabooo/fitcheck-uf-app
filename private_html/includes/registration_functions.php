<?php

require_once 'validation_functions.php';

function register_user($mysqli, $username, $email, $password) {
    global $message, $email_err, $can_resend, $remaining_time;

    $can_resend = true;
    $remaining_time = 0;

    // Start transaction
    $mysqli->begin_transaction();

    try {
        // Insert user into database
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

        // Create email verification data
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 86400);
        $current_time = date("Y-m-d H:i:s", time());

        // Insert verification data into database
        $sql = "INSERT INTO email_verification_tokens (user_id, token, expires, last_sent) VALUES (?, ?, ?, ?)";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("isss", $user_id, $token, $expires, $current_time);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Token insertion failed.");
        }

        // Commit transaction and send verification email
        $mysqli->commit();
        send_verification_email($email, $token);

        $_SESSION['email'] = $email;

        $message = "";
        $email_err = "";
        $can_resend = true;
        $remaining_time = 0;

        render_template("registration_confirmation_template");
        exit;
    } catch (Exception $e) {
        // Rollback transaction if an error occurred
        $mysqli->rollback();
        echo "Oops! Something went wrong. Please try again later.";
    }
}

function handle_registration($mysqli, $ip_address, $max_attempts, $attempt_window) {
    global $username_err, $email_err, $password_err, $confirm_password_err, $rate_limit_err, $message, $can_resend, $remaining_time;

    // Check if the user has exceeded the registration rate limit
    if (check_registration_attempts($mysqli, $ip_address, $max_attempts, $attempt_window)) {
        $rate_limit_err = "Too many registration attempts. Please try again later.";
        sleep(1);
        return;
    }

    // Record registration attempt
    record_registration_attempt($mysqli, $ip_address);
    
    // Validate user input
    $username = validate_username($mysqli);
    $email = validate_email($mysqli);
    $password = validate_password();
    $confirm_password = validate_confirm_password($password);

    // Register user if there are no errors
    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        register_user($mysqli, $username, $email, $password);
    }
}

function check_registration_attempts($mysqli, $ip_address, $max_attempts, $attempt_window) {
    // Check if the user has exceeded the registration rate limit
    $attempt_window_start = date("Y-m-d H:i:s", time() - $attempt_window);
    $sql = "SELECT COUNT(*) FROM registration_attempts WHERE ip_address = ? AND attempt_time >= ?";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $ip_address, $attempt_window_start);
    $stmt->execute();
    $stmt->bind_result($attempt_count);
    $stmt->fetch();
    $stmt->close();

    return $attempt_count >= $max_attempts;
}

function record_registration_attempt($mysqli, $ip_address) {
    // Inserts a new registration attempt into the database
    $sql = "INSERT INTO registration_attempts (ip_address, attempt_time) VALUES (?, NOW())";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    $stmt->close();
}

?>