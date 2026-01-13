<?php
session_start();

$config = require __DIR__ . '/../../config/app.php';

$mysqli = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);
if ($mysqli->connect_errno) {
    die('Connect error: ' . $mysqli->connect_error);
}

// Helpers tên Việt không dấu (giữ alias cũ để tương thích)
function daDangNhap(): bool {
    return !empty($_SESSION['user']);
}

function laQuanTri(): bool {
    return daDangNhap() && isset($_SESSION['user']['vai_tro']) && $_SESSION['user']['vai_tro'] === 'admin';
}

function batBuocDangNhap(): void {
    if (!daDangNhap()) {
        header('Location: ' . duongDan('/auth/login'));
        exit;
    }
}

function batBuocQuanTri(): void {
    if (!laQuanTri()) {
        header('Location: ' . duongDan('/'));
        exit;
    }
}

function dinhDangNgayGio($datetime): string {
    if (empty($datetime)) return '';
    $dt = new DateTime($datetime);
    return $dt->format('d/m/Y H:i');
}

function duongDan(string $path = ''): string {
    global $config;
    $base = rtrim($config['base_path'], '/');
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}

// Alias cũ để không vỡ code đang dùng
function is_logged_in(): bool { return daDangNhap(); }
function is_admin(): bool { return laQuanTri(); }
function require_login(): void { batBuocDangNhap(); }
function require_admin(): void { batBuocQuanTri(); }
function format_datetime($datetime): string { return dinhDangNgayGio($datetime); }
function base_url(string $path = ''): string { return duongDan($path); }

function view_shared_data(): array {
    $user = $_SESSION['user'] ?? null;
    $isLoggedIn = !empty($user);
    $isAdmin = $isLoggedIn && (($user['vai_tro'] ?? '') === 'admin');

    return [
        'currentUser' => $user,
        'isLoggedIn' => $isLoggedIn,
        'isAdmin' => $isAdmin,
        'currentUserName' => $isLoggedIn ? (string)($user['ten'] ?? '') : '',
        'currentUserEmail' => $isLoggedIn ? (string)($user['email'] ?? '') : '',
    ];
}

function view(string $template, array $data = []): void {
    $data = array_merge(view_shared_data(), $data);
    extract($data, EXTR_SKIP);
    $viewFile = __DIR__ . '/../Views/' . $template . '.php';
    if (!file_exists($viewFile)) {
        http_response_code(404);
        echo 'View not found';
        return;
    }
    require $viewFile;
}

function redirect(string $path): void {
    header('Location: ' . duongDan($path));
    exit;
}
