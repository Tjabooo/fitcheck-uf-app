<?php

function render_template($template) {
    // very inconvenient but i have to define every variable here lol please don't judge it's so dumb
    global $invalid_request_err, $password_err, $confirm_password_err,$token, $email, $email_err, $rate_limit_err, $message, $can_resend, $remaining_time;
    
    // Render the specified template
    include 'templates/' . $template . '.php';
}

?>