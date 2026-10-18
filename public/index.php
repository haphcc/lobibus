<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\ChatbotController;
use App\Controllers\HomeController;
use App\Controllers\PaymentController;
use App\Controllers\RecommendationController;
use App\Controllers\TicketController;
use App\Controllers\TripController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Api\BookingApiController;
use App\Controllers\Api\ChatbotApiController;
use App\Controllers\Api\RecommendationApiController;
use App\Controllers\Api\SeatApiController;
use App\Controllers\Api\TripApiController;

require_once dirname(__DIR__) . '/app/Core/Helper.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $file = dirname(__DIR__) . '/app/' . $relative . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/trips/search', [TripController::class, 'search']);
$router->get('/trips/detail', [TripController::class, 'detail']);
$router->get('/booking/select-seat', [BookingController::class, 'selectSeat']);
$router->post('/booking/checkout', [BookingController::class, 'checkout']);
$router->get('/booking/history', [BookingController::class, 'history']);
$router->get('/booking/detail', [BookingController::class, 'detail']);
$router->post('/booking/cancel', [BookingController::class, 'cancel']);
$router->get('/ticket/qr', [TicketController::class, 'showQr']);
$router->get('/payment/method', [PaymentController::class, 'method']);
$router->get('/payment/result', [PaymentController::class, 'result']);
$router->get('/chatbot', [ChatbotController::class, 'index']);
$router->get('/recommendations', [RecommendationController::class, 'index']);
$router->get('/admin', [DashboardController::class, 'index']);

$router->get('/api/trips/search', [TripApiController::class, 'search']);
$router->get('/api/seats', [SeatApiController::class, 'getByTrip']);
$router->post('/api/bookings/create', [BookingApiController::class, 'create']);
$router->post('/api/chatbot/reply', [ChatbotApiController::class, 'reply']);
$router->get('/api/recommendations', [RecommendationApiController::class, 'suggest']);

(new App($router))->run();
