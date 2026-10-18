<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;

final class BookingApiController extends Controller
{
    public function create(): void
    {
        $payload = json_decode(file_get_contents('php://input') ?: '[]', true) ?: $_POST;
        $this->json([
            'message' => 'TODO: persist booking with BookingService.',
            'data' => $payload,
        ], 201);
    }
}
