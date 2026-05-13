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
        Session::start();
        $this->router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
    }
}
