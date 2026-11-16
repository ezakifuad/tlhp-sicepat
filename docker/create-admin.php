<?php
if (PHP_SAPI !== 'cli') {
    exit(1);
}

$username = $argv[1] ?? '';
$name = trim($argv[2] ?? '');
$password = rtrim(fgets(STDIN) ?: '', "\r\n");

if (!preg_match('/^[a-zA-Z0-9._-]{3,50}$/D', $username)
    || $name === '' || preg_match_all('/./us', $name) > 50
    || strlen($password) < 12 || strlen($password) > 72) {
    fwrite(STDERR, "Isi username (3–50), nama (maks. 50 karakter), dan kata sandi (12–72 byte).\n");
    exit(1);
}

require '/var/www/html/koneksi.php';

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, password, nama, level) VALUES (?, ?, ?, 'admin')");
try {
    $stmt->execute([$username, $hash, $name]);
} catch (mysqli_sql_exception $exception) {
    if ($exception->getCode() !== 1062) {
        throw $exception;
    }
    fwrite(STDERR, "Username sudah digunakan. Gunakan username lain.\n");
    exit(1);
}
fwrite(STDOUT, "Akun admin berhasil dibuat.\n");
