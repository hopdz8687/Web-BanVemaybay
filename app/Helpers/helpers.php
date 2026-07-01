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

// --- API helpers (simple, learning-focused) ---

function json_response($payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string
{
    $data = strtr($data, '-_', '+/');
    $pad = strlen($data) % 4;
    if ($pad > 0) {
        $data .= str_repeat('=', 4 - $pad);
    }
    return base64_decode($data);
}

function jwt_encode(array $payload, int $ttlSeconds = 3600): string
{
    global $config;
    $secret = $config['jwt_secret'] ?? 'demo-secret';

    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $iat = time();
    $payload['iat'] = $payload['iat'] ?? $iat;
    $payload['exp'] = $payload['exp'] ?? ($iat + $ttlSeconds);

    $h = base64url_encode(json_encode($header));
    $p = base64url_encode(json_encode($payload));
    $sig = hash_hmac('sha256', "$h.$p", $secret, true);
    $s = base64url_encode($sig);

    return $h . '.' . $p . '.' . $s;
}

function jwt_decode(string $token): ?array
{
    global $config;
    $secret = $config['jwt_secret'] ?? 'demo-secret';

    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    [$h, $p, $s] = $parts;
    $headerJson = base64url_decode($h);
    $payloadJson = base64url_decode($p);

    $header = json_decode($headerJson, true);
    $payload = json_decode($payloadJson, true);
    if (!is_array($header) || !is_array($payload)) {
        return null;
    }

    if (($header['alg'] ?? '') !== 'HS256') {
        return null;
    }

    $expected = base64url_encode(hash_hmac('sha256', "$h.$p", $secret, true));
    if (!hash_equals($expected, $s)) {
        return null;
    }

    if (isset($payload['exp']) && time() > (int)$payload['exp']) {
        return null;
    }

    return $payload;
}

function bearer_token(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($header === '' && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $header = $headers['Authorization'];
        }
    }

    if (preg_match('/Bearer\s+(.*)$/i', $header, $m)) {
        return trim($m[1]);
    }
    return null;
}
