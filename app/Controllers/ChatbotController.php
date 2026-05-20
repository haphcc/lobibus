<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class ChatbotController extends Controller
{
    public function index(): void
    {
        header('Location: ' . \url('/') . '?open_chat=1');
        exit;
    }
}
