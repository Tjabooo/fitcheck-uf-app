<?php

session_start();
require_once 'auth_check.php';
require_once 'db_config.php';

header('Content-Type: application/json');

// Get the POST data
$input = json_decode(file_get_contents('php://input'), true);

// Check for confirmation
if (!isset($input['confirm']) || $input['confirm'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Account deletion not confirmed.']);
    exit;
}

$username = $_SESSION['username'];

// Start transaction
$mysqli->begin_transaction();

try {
    // Fetch user ID
    $sql = "SELECT id FROM users WHERE username = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id);
        if (!$stmt->fetch()) {
            throw new Exception("User not found.");
        }
        $stmt->close();
    } else {
        throw new Exception("Database error.");
    }

    // Delete user from users table
    $sql = "DELETE FROM users WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        throw new Exception("Failed to delete user.");
    }

    // Commit transaction
    $mysqli->commit();

    // Delete profile picture
    $upload_dir = 'assets/profile-pictures/';
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    foreach ($allowed_extensions as $ext) {
        $file_path = $upload_dir . $username . '.' . $ext;
        unlink($file_path);        
    }    

    // Destroy session
    session_unset();
    session_destroy();

    echo json_encode(['success' => true, 'message' => 'Account deleted successfully.']);
} catch (Exception $e) {
    // Rollback transaction
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>