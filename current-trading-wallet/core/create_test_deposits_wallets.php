<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Deposit;

$userId = 6; // testuser@trading-wallet.net

// 1. Create a pending deposit via DB
$existingDeposit = DB::table('deposits')->where('user_id', $userId)->where('amount', '5000')->where('status', 'pending')->first();
if (!$existingDeposit) {
    $depositId = DB::table('deposits')->insertGetId([
        'user_id' => $userId,
        'amount' => '5000',
        'converted_amount' => '5000',
        'currency' => 'USD',
        'charge' => '0',
        'method' => 'Bank Transfer USD',
        'status' => 'pending',
        'txn_id' => substr(md5(uniqid()), 0, 16),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created pending deposit ID: {$depositId}\n";
} else {
    echo "Pending deposit already exists ID: {$existingDeposit->id}\n";
}

// 2. Create trading wallets for ETH and USDT
$ethWallet = DB::table('trading_wallets')->where('user_id', $userId)->where('symbol', 'ETH')->first();
if (!$ethWallet) {
    DB::table('trading_wallets')->insert([
        'user_id' => $userId,
        'symbol' => 'ETH',
        'address' => '0x' . str_pad(dechex(rand(1000, 9999)), 40, '0'),
        'balance' => '0',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created ETH trading wallet\n";
} else {
    echo "ETH trading wallet already exists\n";
}

$usdtWallet = DB::table('trading_wallets')->where('user_id', $userId)->where('symbol', 'USDT')->first();
if (!$usdtWallet) {
    DB::table('trading_wallets')->insert([
        'user_id' => $userId,
        'symbol' => 'USDT',
        'address' => '0x' . str_pad(dechex(rand(1000, 9999)), 40, '0'),
        'balance' => '0',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created USDT trading wallet\n";
} else {
    echo "USDT trading wallet already exists\n";
}

// 3. Show current state
echo "\n=== CURRENT STATE ===\n";
$deposits = DB::table('deposits')->where('user_id', $userId)->get();
echo "Deposits:\n";
foreach ($deposits as $d) {
    echo "  ID:{$d->id} | \${$d->amount} | status:{$d->status} | method:{$d->method}\n";
}

$wallets = DB::table('trading_wallets')->where('user_id', $userId)->get();
echo "Trading Wallets:\n";
foreach ($wallets as $w) {
    echo "  ID:{$w->id} | {$w->symbol} | balance:{$w->balance}\n";
}

// 4. Check user balance
$user = User::find($userId);
echo "\nUser balance: {$user->balance}\n";
echo "User fiat balance: " . ($user->fiat_balance ?? 'N/A') . "\n";
