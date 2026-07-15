<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Deposits table columns ===\n";
$cols = Schema::getColumnListing('deposits');
print_r($cols);

echo "\n=== Latest deposit ===\n";
$dep = DB::table('deposits')->orderBy('id', 'desc')->first();
print_r($dep);

echo "\n=== isAddonEnabled check ===\n";
if (function_exists('isAddonEnabled')) {
    echo "isAddonEnabled exists\n";
    echo "cryptotrading: " . (isAddonEnabled('cryptotrading') ? 'true' : 'false') . "\n";
} else {
    echo "isAddonEnabled does NOT exist\n";
}

echo "\n=== Trading wallets ===\n";
$wallets = DB::table('trading_wallets')->get();
print_r($wallets);

echo "\n=== Website settings (ref_bonus) ===\n";
$refBonus = DB::table('website_settings')->where('name', 'ref_bonus')->first();
print_r($refBonus);
