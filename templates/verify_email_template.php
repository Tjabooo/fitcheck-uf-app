<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification - FitCheck UF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php
        require_once 'includes/functions.php';
        render_template("header");
    ?>

    <main>
        <div class="auth-container">
            <h2>Email Verification</h2>
            <?php if (!empty($verification_err)): ?>
                <div class="error-message"><?php echo htmlspecialchars($verification_err); ?></div>
            <?php else: ?>
                <p>Your email has been successfully verified! You can now <a href="login.php">log in</a>.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>