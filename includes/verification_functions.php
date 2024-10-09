<?php

function send_verification_email($email, $token) {
    // Set email content
    $subject = 'Email Verification - FitCheck UF';
    $verification_link = "https://app.fitcheck.nu/verify_email.php?token=" . urlencode($token) . "&email=" . urlencode($email);
    $message = "
    <html>
    <head>
        <title>Email Verification - FitCheck UF</title>
    </head>
    <body>
        <p>Thank you for registering with FitCheck UF!</p>
        <p>Please click the link below to verify your email address:</p>
        <p><a href='" . $verification_link . "'>Verify Email</a></p>
        <p>If you did not register, please ignore this email.</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= 'From: FitCheck UF Support <noreply@fitcheck.nu>' . "\r\n";

    // Send email
    mail($email, $subject, $message, $headers);
}

function resend_verification_email($mysqli, $email) {
    // Check if the email exists in the session
    $email_err = '';
    $message = '';
    $can_resend = true;
    $remaining_time = 0;

    if (empty($email)) {
        return [
            'email_err' => "Email not found in session.",
            'message' => $message,
            'can_resend' => $can_resend,
            'remaining_time' => $remaining_time
        ];
    }

    // Gets the user's ID and verification data
    $sql = "SELECT id, verified FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        return [
            'email_err' => "Something went wrong. Please try again later.",
            'message' => $message,
            'can_resend' => $can_resend,
            'remaining_time' => $remaining_time
        ];
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows != 1) {
        $stmt->close();
        return [
            'email_err' => "No account found with that email.",
            'message' => $message,
            'can_resend' => $can_resend,
            'remaining_time' => $remaining_time
        ];
    }

    $stmt->bind_result($user_id, $verified);
    $stmt->fetch();
    $stmt->close();

    // Check if the user is already verified
    if ($verified == 1) {
        return [
            'email_err' => "This account is already verified. You can log in.",
            'message' => $message,
            'can_resend' => $can_resend,
            'remaining_time' => $remaining_time
        ];
    }

    // Check if the resend rate limit is respected
    $sql = "SELECT last_sent FROM email_verification_tokens WHERE user_id = ?";
    $stmt_rate = $mysqli->prepare($sql);
    $stmt_rate->bind_param("i", $user_id);
    $stmt_rate->execute();
    $stmt_rate->store_result();

    if ($stmt_rate->num_rows == 1) {
        $stmt_rate->bind_result($last_sent);
        $stmt_rate->fetch();
        $stmt_rate->close();

        $current_time = time();
        $last_sent_time = strtotime($last_sent);
        $time_diff = $current_time - $last_sent_time;

        if ($time_diff < 60) {
            $can_resend = false;
            $remaining_time = 60 - $time_diff;
            return [
                'email_err' => $email_err,
                'message' => $message,
                'can_resend' => $can_resend,
                'remaining_time' => $remaining_time
            ];
        }
    } else {
        $stmt_rate->close();
    }

    // Resend verification email if allowed
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+1 day"));
    $current_time_formatted = date("Y-m-d H:i:s", time());

    // Delete old token if it exists
    $sql = "DELETE FROM email_verification_tokens WHERE user_id = ?";
    $stmt_del = $mysqli->prepare($sql);
    $stmt_del->bind_param("i", $user_id);
    $stmt_del->execute();
    $stmt_del->close();

    // Insert new verification token
    $sql = "INSERT INTO email_verification_tokens (user_id, token, expires, last_sent) VALUES (?, ?, ?, ?)";
    $stmt_insert = $mysqli->prepare($sql);
    if (!$stmt_insert) {
        return [
            'email_err' => "Something went wrong. Please try again later.",
            'message' => $message,
            'can_resend' => $can_resend,
            'remaining_time' => $remaining_time
        ];
    }

    $stmt_insert->bind_param("isss", $user_id, $token, $expires, $current_time_formatted);
    $stmt_insert->execute();
    $stmt_insert->close();

    // Send new verification email
    send_verification_email($email, $token);
    $message = "A new verification email has been sent.";

    return [
        'email_err' => $email_err,
        'message' => $message,
        'can_resend' => $can_resend,
        'remaining_time' => $remaining_time
    ];
}

function handle_resend_verification($mysqli) {
    global $email_err, $message, $can_resend, $remaining_time;
    
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    $resend_result = resend_verification_email($mysqli, $email);
    $email_err = $resend_result['email_err'];
    $message = $resend_result['message'];
    $can_resend = $resend_result['can_resend'];
    $remaining_time = $resend_result['remaining_time'];

    render_template("registration_confirmation_template");
    exit;
}

function verify_email_token($mysqli, $token) {
    $verification_err = "";

    // Check if the token exists and hasn't expired
    $sql = "SELECT user_id, expires FROM email_verification_tokens WHERE token = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        return "Something went wrong. Please try again.";
    }

    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows != 1) {
        $stmt->close();
        return "Invalid token.";
    }

    $stmt->bind_result($user_id, $expires);
    $stmt->fetch();
    $stmt->close();

    // Check if the token has expired
    if (strtotime($expires) < time()) {
        return "Token has expired.";
    }

    // Update user's verification status
    $sql = "UPDATE users SET verified = 1 WHERE id = ?";
    $stmt_update = $mysqli->prepare($sql);
    
    if (!$stmt_update) {
        return "Failed to verify email.";
    }

    $stmt_update->bind_param("i", $user_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Delete the used token
    $sql = "DELETE FROM email_verification_tokens WHERE user_id = ?";
    $stmt_delete = $mysqli->prepare($sql);
    $stmt_delete->bind_param("i", $user_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    return $verification_err;
}

?>