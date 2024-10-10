<?php

function render_template($template) {
    // very inconvenient but i have to define every variable here lol please don't judge it's so dumb
    global $invalid_request_err, $password_err, $confirm_password_err,$token, $email, $email_err, $rate_limit_err, $message, $can_resend, $remaining_time;
    
    // Render the specified template
    include 'templates/' . $template . '.php';
}

// Block desktop access
function blockDesktopAccess() {
    if (0) { // 0 = disable, 1 = enable
        if (!preg_match('/Mobi|Android|iPhone|iPad/i', $_SERVER['HTTP_USER_AGENT'])) {
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Access Restricted</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="https://fonts.googleapis.com/css?family=Outfit" rel="stylesheet">
                <style>
                    body {
                        text-align: center;
                        padding: 50px;
                        font-family: "Outfit", sans-serif;
                    }
                    h1 {
                        color: red;
                    }
                </style>
            </head>
            <body>
                <h1>Access Restricted</h1>
                <p>This site is only accessible on mobile devices. Please visit using a mobile device.</p>
            </body>
            </html>';
            exit;
        }
    }
}

?>