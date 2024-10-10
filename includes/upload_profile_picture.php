<?php
// includes/upload_profile_picture.php

session_start();
require_once 'db_config.php';
require_once 'auth_check.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

if (!isset($_FILES['profile_picture'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
    exit;
}

$file = $_FILES['profile_picture'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'File upload error code: ' . $file['error']]);
    exit;
}

// Validate file type (only allow images)
$allowed_types = ['image/jpeg', 'image/png'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.']);
    exit;
}

$max_size = 2 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'error' => 'File size exceeds 2MB.']);
    exit;
}

// Determine file extension
$extension = '';
switch ($mime_type) {
    case 'image/jpeg':
        $extension = 'jpg';
        break;
    case 'image/png':
        $extension = 'png';
        break;
    default:
        $extension = 'jpg';
}

$target_directory = 'assets/profile-pictures/';
$target_file = $target_directory . $username . '.' . $extension;

// Remove existing profile pictures with different extensions
foreach (['jpg', 'png', 'jpeg'] as $ext) {
    $existing_file = $target_directory . $username . '.' . $ext;
    if (file_exists($existing_file) && $existing_file !== $target_file) {
        unlink($existing_file);
    }
}

// Move uploaded file to target location
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    echo json_encode([
        'success' => true,
        'profile_picture' => "assets/profile-pictures/{$username}.{$extension}"
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file.']);
}

?>