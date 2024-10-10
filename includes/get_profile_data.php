<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'auth_check.php';
require_once 'db_config.php';

// Initialize response
$response = [
    'success' => false,
    'username' => '',
    'email' => '',
    'profile_picture' => 'assets/profile-pictures/default.png' // Default picture
];

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $response['message'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

$username = $_SESSION['username'];

// Fetch user data from the database
$sql = "SELECT email FROM users WHERE username = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($email);
    if ($stmt->fetch()) {
        $stmt->close();

        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        // Iterate through allowed extensions to find the profile picture
        foreach ($allowed_extensions as $ext) {
            $filename = "{$username}.$ext";
            $relative_path = "assets/profile-pictures/{$filename}";
            if (file_exists($relative_path)) {
                $response['profile_picture'] = $relative_path;
                break;
            }
        }
    }
    
    $response['success'] = true;
    $response['username'] = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $response['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
} else {
        $stmt->close();
        $response['message'] = 'User not found.';
    }

// Output JSON response
echo json_encode($response);

?>