<?php

function validate_email($mysqli) {
    global $email_err;

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
        return null;
    }

    $email = trim($_POST["email"]);

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
        return null;
    }

    // Check if email is already registered
    $sql = "SELECT id FROM users WHERE email = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $email_err = "This email is already registered.";
        }

        $stmt->close();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    return $email;
}

function validate_email_login() {
    global $email_err;

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
        return null;
    }

    $email = trim($_POST["email"]);

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
        return null;
    }

    return $email;
}

function validate_password() {
    global $password_err;

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
        return null;
    }

    $password = trim($_POST["password"]);

    // Check if password is at least 8 characters long
    if (strlen($password) < 8) {
        $password_err = "Password must have at least 8 characters.";
        return null;
    }

    return $password;
}

function validate_confirm_password($password) {
    global $confirm_password_err;

    // Check if confirm password is empty
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
        return null;
    }

    $confirm_password = trim($_POST["confirm_password"]);

    // Check if confirm password matches password
    if ($password != $confirm_password) {
        $confirm_password_err = "Passwords did not match.";
    }

    return $confirm_password;
}

function validate_username($mysqli) {
    global $username_err;
    
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
        return null;
    }

    $username = trim($_POST["username"]);

    // Check if username contains any special characters
    if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $username_err = "Username can only contain letters and numbers.";
        return null;
    }

    // Check if username is already taken
    $sql = "SELECT id FROM users WHERE username = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $username_err = "This username is already taken.";
        }

        $stmt->close();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    return $username;
}

?>