<?php
// Database + site settings. On a live server set these as environment variables.
define('WA_NUMBER', getenv('WA_NUMBER') ?: '919022142587'); // country code + number, no + or spaces
define('DB_ENABLED', filter_var(getenv('DB_ENABLED') ?: 'true', FILTER_VALIDATE_BOOLEAN));
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'shiv_kailasa');
define('DB_SSL', filter_var(getenv('DB_SSL') ?: 'false', FILTER_VALIDATE_BOOLEAN)); // true for cloud MySQL (TiDB, Aiven)
define('DB_PORT', (int)(getenv('DB_PORT') ?: 3306));

function db_connect(): mysqli {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = mysqli_init();
    $flags = 0;
    if (DB_SSL) { $conn->ssl_set(null, null, null, null, null); $flags = MYSQLI_CLIENT_SSL; }
    $conn->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, null, $flags);
    return $conn;
}
