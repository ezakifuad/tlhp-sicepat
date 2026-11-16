<?php
ini_set('session.cookie_secure', getenv('SESSION_COOKIE_SECURE') === '1' ? '1' : '0');
session_start();

function requireLogin(): void
{
    if (empty($_SESSION['login']) || empty($_SESSION['user_id'])) {
        http_response_code(401);
        exit('Silakan masuk terlebih dahulu.');
    }
}

function requireAdmin(mysqli $conn): void
{
    $stmt = $conn->prepare('SELECT level FROM users WHERE id = ?');
    $stmt->execute([(int) $_SESSION['user_id']]);
    if (($stmt->get_result()->fetch_assoc()['level'] ?? null) !== 'admin') {
        http_response_code(403);
        exit('Akses khusus admin.');
    }
}

function csrfToken(): string
{
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function requireCsrfToken(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || !hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        exit('Token sesi tidak valid. Muat ulang halaman.');
    }
}
