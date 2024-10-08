<?php

function login_user($mysqli, $email, $password) {
    $email_err = $password_err = "";

    $sql = "SELECT id, username, email, password, verified FROM users WHERE email = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $email_db, $hashed_password, $verified);
                if ($stmt->fetch()) {
                    if ($verified == 0) {
                        $email_err = "Please verify your email before logging in.";
                    } elseif (password_verify($password, $hashed_password)) {
                        session_start();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        header("location: index.php");
                        exit;
                    } else {
                        $password_err = "The password you entered was not valid.";
                    }
                }
            } else {
                $email_err = "No account found with that email.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
    return ['email_err' => $email_err, 'password_err' => $password_err];
}

function handle_password_reset_request($mysqli, $email) {
    $email_err = "";

    $sql = "SELECT id FROM users WHERE email = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $token = bin2hex(random_bytes(32));
                $expires = date("U") + 1800;

                $sql = "DELETE FROM password_resets WHERE email = ?";
                $stmt_del = $mysqli->prepare($sql);
                $stmt_del->bind_param("s", $email);
                $stmt_del->execute();
                $stmt_del->close();

                $sql = "INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)";
                if ($stmt_insert = $mysqli->prepare($sql)) {
                    $stmt_insert->bind_param("sss", $email, $token, $expires);
                    if ($stmt_insert->execute()) {
                        send_reset_email($email, $token);
                        include 'templates/reset_pass_confirmation_template.php';
                        exit;
                    } else {
                        echo "Something went wrong. Please try again later.";
                    }
                    $stmt_insert->close();
                }
            } else {
                $email_err = "No account found with that email.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }

    return $email_err;
}

function reset_user_password($mysqli, $email, $token, $password) {
    $invalid_request_err = "";

    $sql = "SELECT * FROM password_resets WHERE email = ? AND expires >= ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $current_time = date("U");
        $stmt->bind_param("ss", $email, $current_time);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (hash_equals($row['token'], $token)) {
                    $sql = "UPDATE users SET password = ? WHERE email = ?";
                    if ($stmt_update = $mysqli->prepare($sql)) {
                        $new_password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt_update->bind_param("ss", $new_password_hash, $email);
                        if ($stmt_update->execute()) {
                            $sql = "DELETE FROM password_resets WHERE email = ?";
                            if ($stmt_delete = $mysqli->prepare($sql)) {
                                $stmt_delete->bind_param("s", $email);
                                $stmt_delete->execute();
                                $stmt_delete->close();
                            }
                            header("location: login.php?reset=success");
                            exit;
                        } else {
                            $invalid_request_err = "Something went wrong. Please try again.";
                        }
                        $stmt_update->close();
                    }
                } else {
                    $invalid_request_err = "Invalid or expired token.";
                }
            } else {
                $invalid_request_err = "Invalid or expired token.";
            }
        } else {
            $invalid_request_err = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
    return $invalid_request_err;
}

function send_reset_email($email, $token) {
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

    if (mail($email, $subject, $message, $headers)) {
    } else {
        echo "Failed to send the email. Please try again later.";
    }
}

function send_verification_email($email, $token) {
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

    mail($email, $subject, $message, $headers);
}

function delete_expired_tokens($mysqli) {
    $sql = "DELETE FROM email_verification_tokens WHERE expires <= NOW()";
    $mysqli->query($sql);
}

function delete_unverified_users($mysqli) {
    $sql = "DELETE u FROM users u LEFT JOIN email_verification_tokens evt ON u.id = evt.user_id WHERE u.verified = 0 AND evt.id IS NULL";
    $mysqli->query($sql);
}

function record_registration_attempt($mysqli, $ip_address) {
    $sql = "INSERT INTO registration_attempts (ip_address, attempt_time) VALUES (?, NOW())";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    $stmt->close();
}

function check_registration_attempts($mysqli, $ip_address, $max_attempts, $attempt_window) {
    $attempt_window_start = date("Y-m-d H:i:s", time() - $attempt_window);
    $sql = "SELECT COUNT(*) FROM registration_attempts WHERE ip_address = ? AND attempt_time >= ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $ip_address, $attempt_window_start);
    $stmt->execute();
    $stmt->bind_result($attempt_count);
    $stmt->fetch();
    $stmt->close();
    return $attempt_count;
}

function verify_email_token($mysqli, $token) {
    $verification_err = "";

    $sql = "SELECT user_id, expires FROM email_verification_tokens WHERE token = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $token);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $expires);
                $stmt->fetch();

                if (strtotime($expires) >= time()) {
                    $sql = "UPDATE users SET verified = 1 WHERE id = ?";
                    if ($stmt_update = $mysqli->prepare($sql)) {
                        $stmt_update->bind_param("i", $user_id);
                        $stmt_update->execute();
                        $stmt_update->close();

                        $sql = "DELETE FROM email_verification_tokens WHERE user_id = ?";
                        if ($stmt_delete = $mysqli->prepare($sql)) {
                            $stmt_delete->bind_param("i", $user_id);
                            $stmt_delete->execute();
                            $stmt_delete->close();
                        }
                    } else {
                        $verification_err = "Failed to verify email.";
                    }
                } else {
                    $verification_err = "Token has expired.";
                }
            } else {
                $verification_err = "Invalid token.";
            }
        } else {
            $verification_err = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
    return $verification_err;
}

function resend_verification_email($mysqli, $email) {
    $email_err = '';
    $message = '';
    $can_resend = true;
    $remaining_time = 0;

    if (!empty($email)) {
        $sql = "SELECT id, verified FROM users WHERE email = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($user_id, $verified);
                    $stmt->fetch();
                    if ($verified == 0) {
                        $sql = "SELECT last_sent FROM email_verification_tokens WHERE user_id = ?";
                        if ($stmt_rate = $mysqli->prepare($sql)) {
                            $stmt_rate->bind_param("i", $user_id);
                            if ($stmt_rate->execute()) {
                                $stmt_rate->store_result();
                                if ($stmt_rate->num_rows == 1) {
                                    $stmt_rate->bind_result($last_sent);
                                    $stmt_rate->fetch();
                                    $current_time = time();
                                    $last_sent_time = strtotime($last_sent);
                                    $time_diff = $current_time - $last_sent_time;
                                    if ($time_diff < 60) {
                                        $can_resend = false;
                                        $remaining_time = 60 - $time_diff;
                                    }
                                }
                                $stmt_rate->close();
                            } else {
                                $email_err = "Something went wrong. Please try again later.";
                            }
                        }
                        if ($can_resend) {
                            $token = bin2hex(random_bytes(32));
                            $expires = date("Y-m-d H:i:s", strtotime("+1 day"));
                            $current_time_formatted = date("Y-m-d H:i:s", time());

                            $sql = "DELETE FROM email_verification_tokens WHERE user_id = ?";
                            $stmt_del = $mysqli->prepare($sql);
                            $stmt_del->bind_param("i", $user_id);
                            $stmt_del->execute();
                            $stmt_del->close();

                            $sql = "INSERT INTO email_verification_tokens (user_id, token, expires, last_sent) VALUES (?, ?, ?, ?)";
                            if ($stmt_insert = $mysqli->prepare($sql)) {
                                $stmt_insert->bind_param("isss", $user_id, $token, $expires, $current_time_formatted);
                                $stmt_insert->execute();
                                $stmt_insert->close();

                                send_verification_email($email, $token);

                                $message = "A new verification email has been sent.";
                            } else {
                                $email_err = "Something went wrong. Please try again later.";
                            }
                        }
                    } else {
                        $email_err = "This account is already verified. You can log in.";
                    }
                } else {
                    $email_err = "No account found with that email.";
                }
            } else {
                $email_err = "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    } else {
        $email_err = "Email not found in session.";
    }
    return [
        'email_err' => $email_err,
        'message' => $message,
        'can_resend' => $can_resend,
        'remaining_time' => $remaining_time
    ];
}
?>