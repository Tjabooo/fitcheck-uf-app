<header>
    <div class="header">
        <?php if (basename($_SERVER['PHP_SELF']) == 'register.php' or basename($_SERVER['PHP_SELF']) == 'request_pass_reset.php'): ?>
            <div class="header-left">
                <button class="back-button" onclick="history.back();">
                    <img src="assets/back-icon.png" alt="Back Icon">
                </button>
            </div>
        <?php endif; ?>
        <div class="header-center">
            <img src="/assets/logo.png" alt="Logo"/>
            <h1>FitCheck UF</h1>
        </div>
    </div>
</header>