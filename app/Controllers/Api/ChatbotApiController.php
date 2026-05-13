<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Services\ChatbotService;

final class ChatbotApiController extends Controller
{
    public function reply(): void
    {
        $payload = json_decode(file_get_contents('php://input') ?: '[]', true) ?: $_POST;
        $message = (string) ($payload['message'] ?? '');
        $this->json(['reply' => (new ChatbotService())->reply($message)]);
    }
}
