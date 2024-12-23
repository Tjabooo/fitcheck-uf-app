<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - FitCheck UF</title>
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
            <h2>Login</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <input type="password" name="password" placeholder="Password" required>
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <button class="main-button-design" type="submit">Login</button>
                </div>
                <p><a href="request_pass_reset.php">Forgot Password?</a></p>
                <p>Don't have an account? <a href="register.php">Register here</a>.</p>
            </form>
        </div>
    </main>
</body>
</html>