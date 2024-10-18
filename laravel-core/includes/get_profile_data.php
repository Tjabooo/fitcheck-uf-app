<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'auth_check.php';
require_once 'db_config.php';

$response = [
    'success' => false,
    'username' => '',
    'email' => '',
    'profile_picture' => 'assets/profile-pictures/default.png'
];

if (!isset($_SESSION['username'])) {
    $response['message'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

$username = $_SESSION['username'];

$sql = "SELECT email FROM users WHERE username = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($email);
    if ($stmt->fetch()) {
        $stmt->close();

        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        $profile_pictures_dir = __DIR__ . '/../assets/profile-pictures/';

        foreach ($allowed_extensions as $ext) {
            $filename = "{$username}." . strtolower($ext);
            $absolute_path = $profile_pictures_dir . $filename;
            $relative_path = "assets/profile-pictures/{$filename}";
            if (file_exists($absolute_path)) {
                $response['profile_picture'] = $relative_path;
                break;
            }
        }
    } else {
        $stmt->close();
        $response['message'] = 'User not found.';
        echo json_encode($response);
        exit;
    }

    $response['success'] = true;
    $response['username'] = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $response['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
} else {
    $response['message'] = 'Database query failed.';
}

echo json_encode($response);

?>