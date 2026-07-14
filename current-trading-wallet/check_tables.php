<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select('SHOW TABLES');
echo "=== All tables ===\n";
foreach ($tables as $t) {
    $arr = (array)$t;
    $name = array_values($arr)[0];
    if (strpos($name, 'trading') !== false || strpos($name, 'staking') !== false || strpos($name, 'demo') !== false || strpos($name, 'signal') !== false || strpos($name, 'bot') !== false) {
        echo "  TRADING: $name\n";
    }
}
echo "\n=== Check trading_currencies ===\n";
try {
    $cols = DB::select("DESCRIBE trading_currencies");
    foreach ($cols as $col) {
        echo "  {$col->Field} | {$col->Type}\n";
    }
} catch (Exception $e) {
    echo "  Table does not exist: " . $e->getMessage() . "\n";
}

echo "\n=== Check trading_pairs ===\n";
try {
    $cols = DB::select("DESCRIBE trading_pairs");
    foreach ($cols as $col) {
        echo "  {$col->Field} | {$col->Type}\n";
    }
} catch (Exception $e) {
    echo "  Table does not exist: " . $e->getMessage() . "\n";
}

echo "\n=== Check trading_wallets ===\n";
try {
    $cols = DB::select("DESCRIBE trading_wallets");
    foreach ($cols as $col) {
        echo "  {$col->Field} | {$col->Type}\n";
    }
} catch (Exception $e) {
    echo "  Table does not exist: " . $e->getMessage() . "\n";
}
