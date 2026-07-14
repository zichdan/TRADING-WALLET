<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create trading_currencies table
DB::statement("CREATE TABLE IF NOT EXISTS `trading_currencies` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `symbol` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `balance` varchar(255) DEFAULT '0',
    `address` varchar(255) DEFAULT NULL,
    `icon` varchar(255) DEFAULT NULL,
    `status` varchar(255) DEFAULT 'enabled',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
echo "Created trading_currencies table\n";

// Seed with common cryptocurrencies
$currencies = [
    ['symbol' => 'BTC', 'name' => 'Bitcoin'],
    ['symbol' => 'ETH', 'name' => 'Ethereum'],
    ['symbol' => 'USDT', 'name' => 'Tether USD'],
    ['symbol' => 'BNB', 'name' => 'Binance Coin'],
    ['symbol' => 'XRP', 'name' => 'Ripple'],
    ['symbol' => 'ADA', 'name' => 'Cardano'],
    ['symbol' => 'SOL', 'name' => 'Solana'],
    ['symbol' => 'DOGE', 'name' => 'Dogecoin'],
    ['symbol' => 'TRX', 'name' => 'TRON'],
    ['symbol' => 'DOT', 'name' => 'Polkadot'],
    ['symbol' => 'MATIC', 'name' => 'Polygon'],
    ['symbol' => 'LTC', 'name' => 'Litecoin'],
    ['symbol' => 'AVAX', 'name' => 'Avalanche'],
    ['symbol' => 'LINK', 'name' => 'Chainlink'],
    ['symbol' => 'UNI', 'name' => 'Uniswap'],
    ['symbol' => 'ATOM', 'name' => 'Cosmos'],
    ['symbol' => 'XLM', 'name' => 'Stellar'],
    ['symbol' => 'NEAR', 'name' => 'NEAR Protocol'],
    ['symbol' => 'APE', 'name' => 'ApeCoin'],
    ['symbol' => 'FIL', 'name' => 'Filecoin'],
];

$now = now();
foreach ($currencies as $c) {
    $existing = DB::table('trading_currencies')->where('symbol', $c['symbol'])->first();
    if (!$existing) {
        DB::table('trading_currencies')->insert(array_merge($c, [
            'balance' => '0',
            'status' => 'enabled',
            'created_at' => $now,
            'updated_at' => $now,
        ]));
        echo "Inserted: {$c['symbol']} - {$c['name']}\n";
    }
}

echo "\nTotal trading_currencies: " . DB::table('trading_currencies')->count() . "\n";

// Also check if trading_pairs has data
$pairCount = DB::table('trading_pairs')->count();
echo "trading_pairs count: $pairCount\n";
if ($pairCount == 0) {
    DB::table('trading_pairs')->insert([
        'pairs' => json_encode(['BTC/USDT', 'ETH/USDT', 'BNB/USDT', 'SOL/USDT', 'XRP/USDT', 'ADA/USDT', 'DOGE/USDT', 'TRX/USDT', 'DOT/USDT', 'MATIC/USDT', 'LTC/USDT', 'AVAX/USDT']),
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    echo "Inserted trading pairs\n";
}

// Check if staking_coins has data
$stakingCount = DB::table('staking_coins')->count();
echo "staking_coins count: $stakingCount\n";
if ($stakingCount == 0) {
    $stakingCoins = [
        ['symbol' => 'BTC', 'name' => 'Bitcoin', 'apr' => '5', 'min' => '0.001', 'max' => '10', 'duration' => '30'],
        ['symbol' => 'ETH', 'name' => 'Ethereum', 'apr' => '6', 'min' => '0.01', 'max' => '100', 'duration' => '30'],
        ['symbol' => 'USDT', 'name' => 'Tether', 'apr' => '8', 'min' => '10', 'max' => '10000', 'duration' => '30'],
        ['symbol' => 'BNB', 'name' => 'Binance Coin', 'apr' => '7', 'min' => '0.1', 'max' => '1000', 'duration' => '30'],
        ['symbol' => 'SOL', 'name' => 'Solana', 'apr' => '9', 'min' => '0.1', 'max' => '1000', 'duration' => '30'],
    ];
    
    // Check staking_coins table structure
    $cols = DB::select("DESCRIBE staking_coins");
    $colNames = array_map(fn($c) => $c->Field, $cols);
    echo "staking_coins columns: " . implode(', ', $colNames) . "\n";
    
    foreach ($stakingCoins as $coin) {
        $data = array_intersect_key($coin, array_flip($colNames));
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        DB::table('staking_coins')->insert($data);
    }
    echo "Inserted staking coins\n";
}
