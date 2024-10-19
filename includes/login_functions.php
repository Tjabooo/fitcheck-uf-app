<?php

function login_user($mysqli, $email, $password) {
    $email_err = $password_err = "";

    // Check if the email exists and the password is correct
    $sql = "SELECT id, username, email, password, verified FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        echo "Oops! Something went wrong. Please try again later.";
        return ['email_err' => $email_err, 'password_err' => $password_err];
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows != 1) {
        $email_err = "No account found with that email.";
        $stmt->close();
        return ['email_err' => $email_err, 'password_err' => $password_err];
    }

    $stmt->bind_result($id, $username, $email_db, $hashed_password, $verified);
    $stmt->fetch();

    // Check if the user is verified and the password is correct
    if ($verified == 0) {
        $email_err = "Please verify your email before logging in.";
    } elseif (!password_verify($password, $hashed_password)) {
        $password_err = "The password you entered was not valid.";
    } else {
        // If everything is correct, start new session and login the user
        session_start();
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $id;
        $_SESSION["username"] = $username;
        header("location: index.php");
        exit;
    }

    $stmt->close();
    return ['email_err' => $email_err, 'password_err' => $password_err];
}

?>