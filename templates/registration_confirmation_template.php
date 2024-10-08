<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Confirmation - FitCheck UF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <main>
        <div class="auth-container">
            <h2>Registration Successful!</h2>
            <p>We've sent a verification email to your email address. Please check your inbox and click on the verification link to activate your account.</p>
            <form action="register.php" method="post">
                <input type="hidden" name="resend_verification" value="1">
                <div class="form-group">
                    <button class="main-button-design" type="submit" id="resend-button" <?php echo (!$can_resend) ? 'disabled' : ''; ?>>Resend Verification Email</button>
                </div>
            </form>
            <?php if (!$can_resend): ?>
                <p>Please wait <span id="countdown"><?php echo intval($remaining_time); ?></span> seconds before resending.</p>
            <?php endif; ?>
            <?php
            if (!empty($email_err)) {
                echo '<div class="error-message">' . htmlspecialchars($email_err) . '</div>';
            }
            if (!empty($message)) {
                echo '<div class="success-message">' . htmlspecialchars($message) . '</div>';
            }
            ?>
        </div>
    </main>

    <script>
        let remainingTime = <?php echo intval($remaining_time); ?>;
        const resendButton = document.getElementById('resend-button');
        const countdownSpan = document.getElementById('countdown');

        if (remainingTime > 0) {
            const interval = setInterval(() => {
                remainingTime--;
                countdownSpan.textContent = remainingTime;
                if (remainingTime <= 0) {
                    clearInterval(interval);
                    resendButton.disabled = false;
                    countdownSpan.parentElement.style.display = 'none';
                }
            }, 1000);
        }
    </script>
</body>
</html>