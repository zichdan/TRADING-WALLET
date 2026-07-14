<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "App booted successfully\n";
echo "Environment: " . config('app.env') . "\n";
echo "DB: " . config('database.default') . "\n";

try {
    $tables = DB::select('SHOW TABLES');
    echo "Tables in database: " . count($tables) . "\n";
    foreach ($tables as $t) {
        $arr = (array)$t;
        echo "  - " . array_values($arr)[0] . "\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
