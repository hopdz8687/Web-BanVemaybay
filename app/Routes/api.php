<?php

// Minimal API router for /api/v1/*.
// Keep it intentionally simple for learning/demo purposes.

require_once __DIR__ . '/../Controllers/Api/V1/FlightController.php';
require_once __DIR__ . '/../Controllers/Api/V1/AuthController.php';
require_once __DIR__ . '/../Controllers/Api/V1/BookingController.php';
require_once __DIR__ . '/../Controllers/Api/V1/TicketController.php';
require_once __DIR__ . '/../Controllers/Api/V1/Admin/PlaneController.php';
require_once __DIR__ . '/../Controllers/Api/V1/Admin/FlightController.php';
require_once __DIR__ . '/../Controllers/Api/V1/Admin/TicketController.php';
require_once __DIR__ . '/../Controllers/Api/V1/Admin/CustomerController.php';
require_once __DIR__ . '/../Controllers/Api/V1/Admin/RevenueController.php';
require_once __DIR__ . '/../Controllers/Api/V1/Admin/BookingController.php';
require_once __DIR__ . '/../Models/User.php';

function api_method_not_allowed(array $allowed): void
{
    header('Allow: ' . implode(', ', $allowed));
    json_response([
        'ok' => false,
        'message' => 'Method Not Allowed',
    ], 405);
}

function api_require_admin(): void
{
    $user = api_require_auth();
    if (($user['vai_tro'] ?? '') !== 'admin') {
        json_response([
            'ok' => false,
            'message' => 'Forbidden',
        ], 403);
    }
}

function api_current_user(): ?array
{
    if (daDangNhap()) {
        return $_SESSION['user'] ?? null;
    }

    $token = bearer_token();
    if (!$token) {
        return null;
    }

    $payload = jwt_decode($token);
    if (!$payload) {
        return null;
    }

    $userId = (int)($payload['sub'] ?? 0);
    if ($userId <= 0) {
        return null;
    }

    $user = User::findById($userId);
    return $user ?: null;
}

function api_require_auth(): array
{
    $user = api_current_user();
    if (!$user) {
        json_response([
            'ok' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    return $user;
}

function api_send_cors_headers(): void
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');
}

function api_dispatch(string $apiPath, string $method): void
{
    api_send_cors_headers();

    // Preflight request
    if ($method === 'OPTIONS') {
        http_response_code(204);
        exit;
    }

    $apiPath = '/' . trim($apiPath, '/');

    $flightController = new ApiV1FlightController();
    $authController = new ApiV1AuthController();
    $bookingController = new ApiV1BookingController();
    $ticketController = new ApiV1TicketController();
    $adminPlaneController = new ApiV1AdminPlaneController();
    $adminFlightController = new ApiV1AdminFlightController();
    $adminTicketController = new ApiV1AdminTicketController();
    $adminCustomerController = new ApiV1AdminCustomerController();
    $adminRevenueController = new ApiV1AdminRevenueController();
    $adminBookingController = new ApiV1AdminBookingController();

    // Health check
    if ($method === 'GET' && $apiPath === '/ping') {
        json_response([
            'ok' => true,
            'data' => [
                'pong' => true,
                'time' => date('c'),
            ],
            'message' => 'pong',
        ]);
        return;
    }

    // Auth
    if ($apiPath === '/auth/login') {
        if ($method !== 'POST') {
            api_method_not_allowed(['POST']);
        }
        $authController->login();
        return;
    }

    if ($apiPath === '/auth/register') {
        if ($method !== 'POST') {
            api_method_not_allowed(['POST']);
        }
        $authController->register();
        return;
    }

    if ($apiPath === '/auth/me') {
        if ($method !== 'GET') {
            api_method_not_allowed(['GET']);
        }
        $authController->me();
        return;
    }

    if ($apiPath === '/auth/logout') {
        if ($method !== 'POST') {
            api_method_not_allowed(['POST']);
        }
        $authController->logout();
        return;
    }

    // Bookings (customer)
    if ($apiPath === '/bookings') {
        $user = api_require_auth();
        if ($method === 'GET') {
            $bookingController->index($user);
            return;
        }
        if ($method === 'POST') {
            $bookingController->store($user);
            return;
        }
        api_method_not_allowed(['GET', 'POST']);
    }

    if (preg_match('#^/bookings/(\d+)$#', $apiPath, $m)) {
        $user = api_require_auth();
        $id = (int)$m[1];
        if ($method === 'GET') {
            $bookingController->show($user, $id);
            return;
        }
        if ($method === 'PUT' || $method === 'PATCH') {
            $bookingController->update($user, $id);
            return;
        }
        if ($method === 'DELETE') {
            $bookingController->destroy($user, $id);
            return;
        }
        api_method_not_allowed(['GET', 'PUT', 'PATCH', 'DELETE']);
    }

    if (preg_match('#^/bookings/(\d+)/checkout$#', $apiPath, $m)) {
        $user = api_require_auth();
        if ($method !== 'POST') {
            api_method_not_allowed(['POST']);
        }
        $bookingController->checkout($user, (int)$m[1]);
        return;
    }

    // My tickets (customer)
    if ($apiPath === '/my-tickets') {
        $user = api_require_auth();
        if ($method === 'GET') {
            $ticketController->index($user);
            return;
        }
        api_method_not_allowed(['GET']);
    }

    if (preg_match('#^/my-tickets/(\d+)$#', $apiPath, $m)) {
        $user = api_require_auth();
        $id = (int)$m[1];
        if ($method === 'GET') {
            $ticketController->show($user, $id);
            return;
        }
        if ($method === 'PUT' || $method === 'PATCH') {
            $ticketController->updatePassengers($user, $id);
            return;
        }
        api_method_not_allowed(['GET', 'PUT', 'PATCH']);
    }

    // Flights (public)
    if ($apiPath === '/flights') {
        if ($method !== 'GET') {
            api_method_not_allowed(['GET']);
        }
        $flightController->index();
        return;
    }

    if ($apiPath === '/flights/search') {
        if ($method !== 'GET') {
            api_method_not_allowed(['GET']);
        }
        $flightController->search();
        return;
    }

    if (preg_match('#^/flights/(\d+)$#', $apiPath, $m)) {
        if ($method !== 'GET') {
            api_method_not_allowed(['GET']);
        }
        $flightController->show((int)$m[1]);
        return;
    }

    // Admin - Planes
    if ($apiPath === '/admin/planes') {
        api_require_admin();
        if ($method === 'GET') {
            $adminPlaneController->index();
            return;
        }
        if ($method === 'POST') {
            $adminPlaneController->store();
            return;
        }
        api_method_not_allowed(['GET', 'POST']);
    }

    if (preg_match('#^/admin/planes/(\d+)$#', $apiPath, $m)) {
        api_require_admin();
        $id = (int)$m[1];
        if ($method === 'GET') {
            $adminPlaneController->show($id);
            return;
        }
        if ($method === 'PUT' || $method === 'PATCH') {
            $adminPlaneController->update($id);
            return;
        }
        if ($method === 'DELETE') {
            $adminPlaneController->destroy($id);
            return;
        }
        api_method_not_allowed(['GET', 'PUT', 'PATCH', 'DELETE']);
    }

    // Admin - Flights
    if ($apiPath === '/admin/flights') {
        api_require_admin();
        if ($method === 'GET') {
            $adminFlightController->index();
            return;
        }
        if ($method === 'POST') {
            $adminFlightController->store();
            return;
        }
        api_method_not_allowed(['GET', 'POST']);
    }

    if (preg_match('#^/admin/flights/(\d+)$#', $apiPath, $m)) {
        api_require_admin();
        $id = (int)$m[1];
        if ($method === 'GET') {
            $adminFlightController->show($id);
            return;
        }
        if ($method === 'PUT' || $method === 'PATCH') {
            $adminFlightController->update($id);
            return;
        }
        if ($method === 'DELETE') {
            $adminFlightController->destroy($id);
            return;
        }
        api_method_not_allowed(['GET', 'PUT', 'PATCH', 'DELETE']);
    }

    // Admin - Tickets of a flight
    if (preg_match('#^/admin/flights/(\d+)/tickets$#', $apiPath, $m)) {
        api_require_admin();
        $flightId = (int)$m[1];
        if ($method === 'GET') {
            $adminTicketController->listByFlight($flightId);
            return;
        }
        if ($method === 'POST') {
            $adminTicketController->store($flightId);
            return;
        }
        api_method_not_allowed(['GET', 'POST']);
    }

    if (preg_match('#^/admin/tickets/(\d+)$#', $apiPath, $m)) {
        api_require_admin();
        $id = (int)$m[1];
        if ($method === 'GET') {
            $adminTicketController->show($id);
            return;
        }
        if ($method === 'PUT' || $method === 'PATCH') {
            $adminTicketController->update($id);
            return;
        }
        if ($method === 'DELETE') {
            $adminTicketController->destroy($id);
            return;
        }
        api_method_not_allowed(['GET', 'PUT', 'PATCH', 'DELETE']);
    }

    // Admin - Customers
    if ($apiPath === '/admin/customers') {
        api_require_admin();
        if ($method === 'GET') {
            $adminCustomerController->index();
            return;
        }
        if ($method === 'POST') {
            $adminCustomerController->store();
            return;
        }
        api_method_not_allowed(['GET', 'POST']);
    }

    if (preg_match('#^/admin/customers/(\d+)$#', $apiPath, $m)) {
        api_require_admin();
        $id = (int)$m[1];
        if ($method === 'GET') {
            $adminCustomerController->show($id);
            return;
        }
        if ($method === 'PUT' || $method === 'PATCH') {
            $adminCustomerController->update($id);
            return;
        }
        if ($method === 'DELETE') {
            $adminCustomerController->destroy($id);
            return;
        }
        api_method_not_allowed(['GET', 'PUT', 'PATCH', 'DELETE']);
    }

    // Admin - Revenue
    if ($apiPath === '/admin/revenue') {
        api_require_admin();
        if ($method !== 'GET') {
            api_method_not_allowed(['GET']);
        }
        $adminRevenueController->summary();
        return;
    }

    // Admin - Bookings
    if ($apiPath === '/admin/bookings') {
        api_require_admin();
        if ($method === 'GET') {
            $adminBookingController->index();
            return;
        }
        api_method_not_allowed(['GET']);
    }

    if (preg_match('#^/admin/bookings/(\d+)$#', $apiPath, $m)) {
        api_require_admin();
        if ($method === 'GET') {
            $adminBookingController->show((int)$m[1]);
            return;
        }
        api_method_not_allowed(['GET']);
    }

    json_response([
        'ok' => false,
        'message' => 'Not Found',
    ], 404);
}
