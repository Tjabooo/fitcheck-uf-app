<?php
require_once 'db_config.php';

function delete_expired_tokens($mysqli) {
    $sql = "DELETE FROM email_verification_tokens WHERE expires < NOW()";
    $mysqli->query($sql);
}

function delete_expired_password_resets($mysqli) {
    $sql = "DELETE FROM password_resets WHERE expires < NOW()";
    $mysqli->query($sql);
}

function delete_unverified_users($mysqli) {
    $time_limit = date('Y-m-d H:i:s', strtotime('-24 hours'));

    $sql = "DELETE FROM users WHERE verified = 0 AND created_at < ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $time_limit);
    $stmt->execute();
    $stmt->close();
}
?>