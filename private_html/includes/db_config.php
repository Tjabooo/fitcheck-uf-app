<?php
define('DB_SERVER', 's685.loopia.se');
define('DB_USERNAME', 'admin@f365386');
define('DB_PASSWORD', '[(%VL@r\'mNeG');
define('DB_NAME', 'fitcheck_nu_db_1');

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
?>