<?php
require_once 'includes/auth_check.php';
require_once 'includes/functions.php';

render_template("header");
?>

<head>
    <meta charset="UTF-8">
    <title>FitCheck UF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<main id="content">
    <!-- Dynamic content handled by main.js -->
</main>

<?php
render_template("nav");
?>

<script src="assets/js/main.js"></script>