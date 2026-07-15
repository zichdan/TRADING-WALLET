<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = app('router')->getRoutes();
$tradingRoutes = [];
foreach ($routes as $route) {
    $uri = $route->uri();
    $name = $route->getName();
    $methods = implode('|', $route->methods());
    // Filter for trade, wallet, deposit related routes
    if (preg_match('/trade|wallet|deposit|crypto/i', $uri) || ($name && preg_match('/trade|wallet|deposit|crypto/i', $name))) {
        $tradingRoutes[] = "$methods $uri => $name";
    }
}
foreach ($tradingRoutes as $r) {
    echo $r . "\n";
}
