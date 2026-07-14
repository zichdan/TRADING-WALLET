<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    $name = $route->getName();
    if ($name && (str_contains($name, 'trading.demo') || str_contains($name, 'staking') || str_contains($name, 'trading.trade'))) {
        echo "$name => " . url($route->uri()) . "\n";
    }
}
