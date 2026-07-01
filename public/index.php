<?php
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/CustomerController.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';
require_once __DIR__ . '/../app/Helpers/helpers.php';
require_once __DIR__ . '/../app/Routes/api.php';

$auth = new AuthController();
$customer = new CustomerController();
$admin = new AdminController();

//chuẩn hóa url web
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$config = require __DIR__ . '/../config/app.php';
$base = rtrim($config['base_path'], '/');
if ($base && strpos($path, $base) === 0) {
    $path = substr($path, strlen($base));
}
$path = '/' . trim($path, '/');

// Serve static assets (images, css, js) from the /assets directory
if (strpos($path, '/assets/') === 0) {
    $file = realpath(__DIR__ . '/..' . $path);
    $assetsRoot = realpath(__DIR__ . '/../assets');

    // Basic security: ensure requested file is really inside /assets
    if ($file && $assetsRoot && strpos($file, $assetsRoot) === 0 && is_file($file)) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'png':  $mime = 'image/png'; break;
            case 'jpg':
            case 'jpeg': $mime = 'image/jpeg'; break;
            case 'gif':  $mime = 'image/gif'; break;
            case 'webp': $mime = 'image/webp'; break;
            case 'css':  $mime = 'text/css'; break;
            case 'js':   $mime = 'application/javascript'; break;
            default:     $mime = 'application/octet-stream';
        }
        header('Content-Type: ' . $mime);
        readfile($file);
        exit;
    }

    http_response_code(404);
    echo '404 Not Found';
    exit;
}

// API routing: /api/v1/*
if (strpos($path, '/api/v1') === 0) {
    $apiPath = substr($path, strlen('/api/v1'));
    if ($apiPath === false || $apiPath === '') {
        $apiPath = '/';
    }

    try {
        api_dispatch($apiPath, $_SERVER['REQUEST_METHOD'] ?? 'GET');
    } catch (Throwable $e) {
        // Temporary debug output for development — include exception message and trace.
        // IMPORTANT: revert this change before deploying to production.
        json_response([
            'ok' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
    exit;
}

switch ($path) {
    case '/':
        if (!is_logged_in()) {
            redirect('/auth/login');
        }
        if (is_admin()) {
            redirect('/admin/dashboard');
        }
        redirect('/customer/dashboard');
        break;

    // Auth
    case '/auth/login':
        $auth->login();
        break;
    case '/auth/register':
        $auth->register();
        break;
    case '/auth/logout':
        $auth->logout();
        break;
    case '/auth/change-profile':
        $auth->changeProfile();
        break;

    case '/demo/api':
        view('demo/api');
        break;

    // Customer
    case '/customer/dashboard':
        $customer->dashboard();
        break;
    case '/customer/search':
        $customer->search();
        break;
    case '/customer/book':
        $customer->book();
        break;
    case '/customer/cart':
        $customer->cart();
        break;
    case '/customer/cart/edit':
        $customer->editCart();
        break;
    case '/customer/checkout':
        $customer->checkout();
        break;
    case '/customer/my-tickets':
        $customer->myTickets();
        break;
    case '/customer/edit-ticket':
        $customer->editTicket();
        break;
    case '/customer/delete-account':
        $customer->deleteAccount();
        break;

    // Admin
    case '/admin/dashboard':
        $admin->dashboard();
        break;
    case '/admin/flights':
        $admin->flights();
        break;
    case '/admin/flights/create':
        $admin->createFlight();
        break;
    case '/admin/flights/edit':
        $admin->editFlight();
        break;
    case '/admin/flights/tickets':
        $admin->flightTickets();
        break;
    case '/admin/flights/tickets/create':
        $admin->createFlightTicket();
        break;
    case '/admin/flights/tickets/edit':
        $admin->editFlightTicket();
        break;
    case '/admin/bookings':
        $admin->bookings();
        break;

    case '/admin/revenue':
        $admin->revenue();
        break;

    case '/admin/customers':
        $admin->customers();
        break;
    case '/admin/customers/create':
        $admin->createCustomer();
        break;
    case '/admin/customers/edit':
        $admin->editCustomer();
        break;

    case '/admin/planes':
        $admin->planes();
        break;
    case '/admin/planes/create':
        $admin->createPlane();
        break;
    case '/admin/planes/edit':
        $admin->editPlane();
        break;

    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
