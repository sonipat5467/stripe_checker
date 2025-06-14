<?php
function load_env($path = __DIR__ . '/.env') {
    if (!file_exists($path)) {
        throw new Exception(".env file not found.");
    }
    return parse_ini_file($path);
}
?>
