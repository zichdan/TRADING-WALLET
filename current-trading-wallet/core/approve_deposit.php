<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

$userId = 6;

// Approve the pending deposit
$deposit = DB::table('deposits')->where('user_id', $userId)->where('status', 'pending')->first();
if ($deposit) {
    DB::table('deposits')->where('id', $deposit->id)->update(['status' => 'approved']);
    echo "Deposit ID:{$deposit->id} approved\n";
    
    // Credit user balance
    $user = User::find($userId);
    $currentBalance = floatval($user->balance);
    $newBalance = $currentBalance + floatval($deposit->amount);
    $user->balance = (string)$newBalance;
    $user->save();
    echo "User balance updated: {$currentBalance} -> {$newBalance}\n";
} else {
    echo "No pending deposit found\n";
}

// Also credit USDT trading wallet with some balance for trading
$usdtWallet = DB::table('trading_wallets')->where('user_id', $userId)->where('symbol', 'USDT')->first();
if ($usdtWallet) {
    DB::table('trading_wallets')->where('id', $usdtWallet->id)->update(['balance' => '5000']);
    echo "USDT wallet balance set to 5000\n";
}

$ethWallet = DB::table('trading_wallets')->where('user_id', $userId)->where('symbol', 'ETH')->first();
if ($ethWallet) {
    DB::table('trading_wallets')->where('id', $ethWallet->id)->update(['balance' => '2.5']);
    echo "ETH wallet balance set to 2.5\n";
}

echo "\n=== FINAL STATE ===\n";
$user = User::find($userId);
echo "User balance: {$user->balance}\n";
$wallets = DB::table('trading_wallets')->where('user_id', $userId)->get();
foreach ($wallets as $w) {
    echo "Wallet: {$w->symbol} = {$w->balance}\n";
}
$deposits = DB::table('deposits')->where('user_id', $userId)->get();
foreach ($deposits as $d) {
    echo "Deposit: ID:{$d->id} \${$d->amount} status:{$d->status}\n";
}
