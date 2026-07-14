<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$now = now();

$stakingCoins = [
    ['coin' => 'Bitcoin', 'symbol' => 'BTC', 'apr' => '5', 'min_stake' => '0.001', 'max_stake' => '10', 'duration' => '30', 'daily_return' => '0.016', 'total' => '100', 'staked' => '0', 'price' => '62000', 'status' => 'enabled', 'icon' => 'btc.png'],
    ['coin' => 'Ethereum', 'symbol' => 'ETH', 'apr' => '6', 'min_stake' => '0.01', 'max_stake' => '100', 'duration' => '30', 'daily_return' => '0.02', 'total' => '1000', 'staked' => '0', 'price' => '1800', 'status' => 'enabled', 'icon' => 'eth.png'],
    ['coin' => 'Tether', 'symbol' => 'USDT', 'apr' => '8', 'min_stake' => '10', 'max_stake' => '10000', 'duration' => '30', 'daily_return' => '0.027', 'total' => '100000', 'staked' => '0', 'price' => '1', 'status' => 'enabled', 'icon' => 'usdt.png'],
    ['coin' => 'Binance Coin', 'symbol' => 'BNB', 'apr' => '7', 'min_stake' => '0.1', 'max_stake' => '1000', 'duration' => '30', 'daily_return' => '0.023', 'total' => '10000', 'staked' => '0', 'price' => '300', 'status' => 'enabled', 'icon' => 'bnb.png'],
    ['coin' => 'Solana', 'symbol' => 'SOL', 'apr' => '9', 'min_stake' => '0.1', 'max_stake' => '1000', 'duration' => '30', 'daily_return' => '0.03', 'total' => '10000', 'staked' => '0', 'price' => '150', 'status' => 'enabled', 'icon' => 'sol.png'],
];

foreach ($stakingCoins as $coin) {
    $existing = DB::table('staking_coins')->where('symbol', $coin['symbol'])->first();
    if (!$existing) {
        DB::table('staking_coins')->insert(array_merge($coin, ['created_at' => $now, 'updated_at' => $now]));
        echo "Inserted staking coin: {$coin['symbol']}\n";
    }
}

echo "Total staking_coins: " . DB::table('staking_coins')->count() . "\n";
