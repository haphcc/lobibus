<?php

declare(strict_types=1);

namespace App\Core;

final class App
{
    public function __construct(private Router $router)
    {
    }

    public function run(): void
    {
        ini_set('default_charset', 'UTF-8');
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }

        Session::start();
        $this->router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
    }
}
