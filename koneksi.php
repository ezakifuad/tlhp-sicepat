<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(
        getenv('DB_HOST') ?: 'localhost',
        getenv('DB_USERNAME') ?: 'sicapat',
        getenv('DB_PASSWORD') ?: '',
        getenv('DB_DATABASE') ?: 'tlhp_db',
        (int) (getenv('DB_PORT') ?: 3306)
    );
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log('SI-CAPAT database connection failed: ' . $exception->getMessage());
    if (PHP_SAPI === 'cli') {
        exit(1);
    }
    http_response_code(503);
    exit('Koneksi database tidak tersedia.');
}
