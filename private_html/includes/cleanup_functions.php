<?php

/** These are currently unused. I'm thinking of implementing a cron job of sorts *
 * to run these functions at regular intervals. *
 * We'll see. *
 */

// Delete expired email verification tokens
function delete_expired_tokens($mysqli) {
    $sql = "DELETE FROM email_verification_tokens WHERE expires <= NOW()";
    $mysqli->query($sql);
}

// Delete expired password reset tokens
function delete_expired_password_resets($mysqli) {
    $sql = "DELETE FROM password_resets WHERE expires <= NOW()";
    $mysqli->query($sql);
}

// Delete unverified users created over 24 hours ago
function delete_unverified_users($mysqli) {
    $time_limit = date('Y-m-d H:i:s', strtotime('-24 hours'));

    $sql = "DELETE FROM users WHERE verified = 0 AND created_at < ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $time_limit);
    $stmt->execute();
    $stmt->close();
}

?>