<?php
require __DIR__ . '/auth.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit;
}
requireCsrfToken();
require __DIR__ . '/koneksi.php';
requireAdmin($conn);

$action = $_POST['aksi'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!in_array($action, ['tambah_akun', 'update_akun', 'hapus_akun'], true)
    || ($action !== 'tambah_akun' && !$id)) {
    http_response_code(400);
    exit('Data akun tidak valid.');
}

if ($action === 'hapus_akun') {
    if ($id === (int) $_SESSION['user_id']) {
        http_response_code(400);
        exit('Tidak bisa menghapus akun sendiri.');
    }
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND username <> 'admin'");
    $stmt->execute([$id]);
    $_SESSION['success'] = $stmt->affected_rows ? 'Akun berhasil dihapus' : 'Akun tidak dapat dihapus';
} else {
    $name = trim((string) ($_POST['nama'] ?? ''));
    $username = trim((string) ($_POST['username'] ?? ''));
    $level = $_POST['level'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($name === '' || $username === '' || !in_array($level, ['admin', 'user'], true)
        || !is_string($password) || ($action === 'tambah_akun' && $password === '')) {
        http_response_code(400);
        exit('Data akun tidak lengkap.');
    }

    try {
        if ($action === 'tambah_akun') {
            $stmt = $conn->prepare('INSERT INTO users (nama, username, level, password) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $username, $level, password_hash($password, PASSWORD_DEFAULT)]);
        } elseif ($password !== '') {
            $stmt = $conn->prepare('UPDATE users SET nama = ?, username = ?, level = ?, password = ? WHERE id = ?');
            $stmt->execute([$name, $username, $level, password_hash($password, PASSWORD_DEFAULT), $id]);
        } else {
            $stmt = $conn->prepare('UPDATE users SET nama = ?, username = ?, level = ? WHERE id = ?');
            $stmt->execute([$name, $username, $level, $id]);
        }
        $_SESSION['success'] = $action === 'tambah_akun' ? 'Akun berhasil ditambahkan' : 'Akun berhasil diperbarui';
    } catch (mysqli_sql_exception $exception) {
        if ($exception->getCode() !== 1062) {
            throw $exception;
        }
        $_SESSION['success'] = 'Username sudah digunakan';
    }
}

header('Location: akun.php', true, 303);
exit;
