<?php

declare(strict_types=1);

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

$chatbotReply = (new App\Services\ChatbotService())->reply('thanh toan');
$recommendations = (new App\Services\RecommendationService())->suggest();
$summary = (new App\Models\Statistic())->dashboardSummary();

assert($chatbotReply !== '');
assert(count($recommendations) > 0);
assert($summary['users'] >= 0);
assert($summary['trips'] >= 0);
assert($summary['bookings'] >= 0);

echo "Member 5 smoke test passed.\n";
