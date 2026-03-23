<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'webform_db';
$dbPort = (int) (getenv('DB_PORT') ?: 3306);

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    exit('Database connection failed. Please check db.php credentials.');
}
?>
