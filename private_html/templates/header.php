<header>
    <div class="header">
        <?php if (basename($_SERVER['PHP_SELF']) == 'request_pass_reset.php'): ?>
            <div class="header-left">
                <button class="main-button-design" onclick=location.href="login.php">
                    Back to Login
                </button>
            </div>
        <?php endif; ?>
        <div class="header-center">
            <img src="/assets/logo.png" alt="Logo"/>
            <h1>FitCheck UF</h1>
        </div>
        <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
            <div class="header-right">
                <img src="/assets/settings-icon.png" id="settings-link" alt="Settings Icon" style="display: none;"/>
            </div>
        <?php endif; ?>
    </div>
</header>