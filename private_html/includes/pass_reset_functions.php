<?php

require_once 'includes/validation_functions.php';

function handle_password_reset_request($mysqli, $email) {
    $email_err = "";

    // Prepare statement to check if the email exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        echo "Oops! Something went wrong. Please try again later.";
        return $email_err;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if email exists in the database
    if ($stmt->num_rows != 1) {
        $email_err = "No account found with that email.";
        $stmt->close();
        return $email_err;
    }

    // Generate reset token and expiry time
    $token = bin2hex(random_bytes(32));
    $expires = date("U") + 1800;

    // Remove any existing password reset requests for this email
    $stmt->close();
    $sql = "DELETE FROM password_resets WHERE email = ?";
    $stmt_del = $mysqli->prepare($sql);
    $stmt_del->bind_param("s", $email);
    $stmt_del->execute();
    $stmt_del->close();

    // Insert new password reset request
    $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
    $stmt_insert = $mysqli->prepare($sql);

    if (!$stmt_insert) {
        echo "Something went wrong. Please try again later.";
        return $email_err;
    }

    $stmt_insert->bind_param("sss", $email, $token, $expires);
    if ($stmt_insert->execute()) {
        send_reset_email($email, $token);
        render_template("reset_pass_confirmation_template");
        exit;
    } else {
        echo "Something went wrong. Please try again later.";
    }

    $stmt_insert->close();
    return $email_err;
}

function reset_user_password($mysqli, $email, $token, $password) {
    $invalid_request_err = "";

    // Check for valid password reset request
    $sql = "SELECT * FROM password_resets WHERE email = ? AND expires >= ?";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        $invalid_request_err = "Something went wrong. Please try again.";
        return $invalid_request_err;
    }

    $current_time = date("U");
    $stmt->bind_param("ss", $email, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    // Check if token is valid
    if (!$row || !hash_equals($row['token'], $token)) {
        return "Invalid or expired token.";
    }

    // Update user password
    $sql = "UPDATE users SET password = ? WHERE email = ?";
    $stmt_update = $mysqli->prepare($sql);
    
    if (!$stmt_update) {
        return "Something went wrong. Please try again.";
    }

    $new_password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt_update->bind_param("ss", $new_password_hash, $email);

    if ($stmt_update->execute()) {
        // Delete password reset request after successful password update
        $stmt_update->close();
        $sql = "DELETE FROM password_resets WHERE email = ?";
        $stmt_delete = $mysqli->prepare($sql);
        $stmt_delete->bind_param("s", $email);
        $stmt_delete->execute();
        $stmt_delete->close();

        header("location: login.php?reset=success");
        exit;
    } else {
        $invalid_request_err = "Something went wrong. Please try again.";
    }

    $stmt_update->close();
    return $invalid_request_err;
}

function send_reset_email($email, $token) {
    // Set email content
    $subject = 'Reset Password - FitCheck UF';
    $url = 'https://app.fitcheck.nu/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email);
    $message = "
    <html>
    <head>
      <title>Reset Password - FitCheck UF</title>
    </head>
    <body>
      <p>To reset your password, please click the link below:</p>
      <p><a href='$url'>Reset Password</a></p>
      <p>If you did not request a password reset, please ignore this email.</p>
    </body>
    </html>
    ";

    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: FitCheck UF Support <noreply@fitcheck.nu>" . "\r\n";

    // Send email
    mail($email, $subject, $message, $headers);
}

function handle_password_reset($mysqli) {
    global $token, $email, $password_err, $confirm_password_err, $invalid_request_err;

    // Validate user input
    $token = $_POST['token'];
    $email = $_POST['email'];

    $password = validate_password();
    $confirm_password = validate_confirm_password($password);

    // Reset user password if there are no errors
    if (empty($password_err) && empty($confirm_password_err)) {
        $invalid_request_err = reset_user_password($mysqli, $email, $token, $password);
    }
}

?>