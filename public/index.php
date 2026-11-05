<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Router;
use App\Controllers\AccountController;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\ChatbotController;
use App\Controllers\HomeController;
use App\Controllers\NewsController;
use App\Controllers\PaymentController;
use App\Controllers\RecommendationController;
use App\Controllers\TicketController;
use App\Controllers\TripController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\BookingController as AdminBookingController;
use App\Controllers\Admin\BusController;
use App\Controllers\Admin\LocationController;
use App\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Controllers\Admin\RouteController;
use App\Controllers\Admin\SeatController;
use App\Controllers\Admin\TripController as AdminTripController;
use App\Controllers\Admin\UserController;
use App\Controllers\Api\BookingApiController;
use App\Controllers\Api\ChatbotApiController;
use App\Controllers\Api\RecommendationApiController;
use App\Controllers\Api\SeatApiController;
use App\Controllers\Api\TripApiController;

$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

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
$router->get('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/auth/google/redirect', [AuthController::class, 'googleRedirect']);
$router->get('/auth/google/callback', [AuthController::class, 'googleCallback']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/account', [AccountController::class, 'index']);
$router->post('/account/profile', [AccountController::class, 'updateProfile']);
$router->post('/account/password/request-otp', [AccountController::class, 'requestPasswordOtp']);
$router->post('/account/password', [AccountController::class, 'updatePassword']);
$router->get('/trips/search', [TripController::class, 'search']);
$router->get('/trips/schedule', [TripController::class, 'schedule']);
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
$router->get('/news', [NewsController::class, 'index']);
$router->get('/news/detail', [NewsController::class, 'detail']);
$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/users', [UserController::class, 'index']);
$router->get('/admin/users/create', [UserController::class, 'create']);
$router->post('/admin/users/store', [UserController::class, 'store']);
$router->get('/admin/users/edit', [UserController::class, 'edit']);
$router->post('/admin/users/update', [UserController::class, 'update']);
$router->post('/admin/users/lock', [UserController::class, 'lock']);
$router->post('/admin/users/unlock', [UserController::class, 'unlock']);
$router->post('/admin/users/delete', [UserController::class, 'delete']);
$router->get('/admin/locations', [LocationController::class, 'index']);
$router->get('/admin/locations/create', [LocationController::class, 'create']);
$router->post('/admin/locations/store', [LocationController::class, 'store']);
$router->get('/admin/locations/edit', [LocationController::class, 'edit']);
$router->post('/admin/locations/update', [LocationController::class, 'update']);
$router->post('/admin/locations/delete', [LocationController::class, 'delete']);
$router->get('/admin/routes', [RouteController::class, 'index']);
$router->get('/admin/routes/create', [RouteController::class, 'create']);
$router->post('/admin/routes/store', [RouteController::class, 'store']);
$router->get('/admin/routes/edit', [RouteController::class, 'edit']);
$router->post('/admin/routes/update', [RouteController::class, 'update']);
$router->post('/admin/routes/delete', [RouteController::class, 'delete']);
$router->get('/admin/buses', [BusController::class, 'index']);
$router->get('/admin/buses/create', [BusController::class, 'create']);
$router->post('/admin/buses/store', [BusController::class, 'store']);
$router->get('/admin/buses/edit', [BusController::class, 'edit']);
$router->post('/admin/buses/update', [BusController::class, 'update']);
$router->post('/admin/buses/delete', [BusController::class, 'delete']);
$router->get('/admin/seats', [SeatController::class, 'index']);
$router->get('/admin/seats/create', [SeatController::class, 'create']);
$router->post('/admin/seats/store', [SeatController::class, 'store']);
$router->post('/admin/seats/delete', [SeatController::class, 'delete']);
$router->get('/admin/trips', [AdminTripController::class, 'index']);
$router->get('/admin/trips/create', [AdminTripController::class, 'create']);
$router->post('/admin/trips/store', [AdminTripController::class, 'store']);
$router->get('/admin/trips/edit', [AdminTripController::class, 'edit']);
$router->post('/admin/trips/update', [AdminTripController::class, 'update']);
$router->post('/admin/trips/delete', [AdminTripController::class, 'delete']);
$router->get('/admin/bookings', [AdminBookingController::class, 'index']);
$router->get('/admin/bookings/detail', [AdminBookingController::class, 'detail']);
$router->post('/admin/bookings/update-status', [AdminBookingController::class, 'updateStatus']);
$router->get('/admin/payments', [AdminPaymentController::class, 'index']);
$router->post('/admin/payments/update-status', [AdminPaymentController::class, 'updateStatus']);

$router->get('/api/trips/search', [TripApiController::class, 'search']);
$router->get('/api/seats', [SeatApiController::class, 'getByTrip']);
$router->post('/api/bookings/create', [BookingApiController::class, 'create']);
$router->post('/api/chatbot/reply', [ChatbotApiController::class, 'reply']);
$router->get('/api/recommendations', [RecommendationApiController::class, 'suggest']);

(new App($router))->run();
