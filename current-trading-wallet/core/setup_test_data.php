<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ManualDepositMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// 1. Create a manual deposit method (Bank Transfer - USD)
$method = ManualDepositMethod::where('name', 'Bank Transfer USD')->first();
if (!$method) {
    $method = new ManualDepositMethod();
    $method->name = 'Bank Transfer USD';
    $method->type = 'bank';
    $method->currency = 'USD';
    $method->min_amount = 100;
    $method->max_amount = 1000000;
    $method->charge = 0;
    $method->charge_type = 'fixed';
    $method->status = 'active';
    $method->payment_instruction = 'Transfer funds to Bank Account: 1234567890, Routing: 021000021, Name: Trading Wallet Inc.';
    $method->logo = 'bank-transfer.png';
    $method->bank_name = 'Test Bank';
    $method->account_name = 'Trading Wallet Inc.';
    $method->account_no = '1234567890';
    $method->sort_code = '021000021';
    $method->save();
    echo "Created deposit method: Bank Transfer USD (ID: {$method->id})\n";
} else {
    echo "Deposit method already exists: Bank Transfer USD (ID: {$method->id})\n";
}

// 2. Ensure user has verified email and id
$user = User::where('email', 'testuser@trading-wallet.net')->first();
if ($user) {
    $user->email_verified = 'verified';
    $user->id_verified = 'verified';
    $user->status = 'active';
    $user->save();
    echo "User verified: {$user->email} (ID: {$user->id})\n";
}

// 3. Ensure settings are correct
$settings = [
    'email_verification' => 'disabled',
    'id_verification' => 'disabled',
    'google_captcha' => 'disabled',
    'trader_mode' => 'enabled',
    'kyc_mode' => 'off',
];
foreach ($settings as $name => $value) {
    $existing = DB::table('website_settings')->where('name', $name)->first();
    if ($existing) {
        DB::table('website_settings')->where('name', $name)->update(['value' => $value]);
    } else {
        DB::table('website_settings')->insert(['name' => $name, 'value' => $value]);
    }
    echo "Setting: {$name} = {$value}\n";
}

// 4. Check trading currencies have addresses
$currencies = DB::table('trading_currencies')->where('status', 'enabled')->get();
foreach ($currencies as $c) {
    if (!$c->address) {
        DB::table('trading_currencies')->where('id', $c->id)->update(['address' => '0x' . str_pad(dechex($c->id), 40, '0')]);
    }
}
echo "Trading currencies checked/updated with addresses\n";

echo "\nSetup complete!\n";
echo "\n=== TEST CREDENTIALS ===\n";
echo "ADMIN:  admin@trading-wallet.net / Admin@2026!\n";
echo "USER:   testuser@trading-wallet.net / User@2026!\n";
