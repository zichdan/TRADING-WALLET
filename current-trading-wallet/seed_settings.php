<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WebsiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Insert essential website settings
$settings = [
    ['name' => 'theme', 'value' => 'prius'],
    ['name' => 'site_name', 'value' => 'Trading Wallet'],
    ['name' => 'site_title', 'value' => 'Trading Wallet - Crypto Trading Platform'],
    ['name' => 'site_description', 'value' => 'Trade cryptocurrencies with ease on our secure platform.'],
    ['name' => 'site_keywords', 'value' => 'crypto, trading, wallet, bitcoin, ethereum, usdt'],
    ['name' => 'general_currency', 'value' => 'USD'],
    ['name' => 'decimal_places', 'value' => '2'],
    ['name' => 'ref_bonus', 'value' => '5'],
    ['name' => 'transfer_auto_approve', 'value' => 'enabled'],
    ['name' => 'site_email', 'value' => 'support@trading-wallet.net'],
    ['name' => 'site_phone', 'value' => '+1234567890'],
    ['name' => 'site_address', 'value' => '123 Trading Street, Crypto City'],
    ['name' => 'site_logo', 'value' => 'logo.png'],
    ['name' => 'site_favicon', 'value' => 'favicon.png'],
    ['name' => 'maintenance_mode', 'value' => '0'],
    ['name' => 'registration', 'value' => 'enabled'],
    ['name' => 'email_verification', 'value' => 'disabled'],
    ['name' => 'kyc', 'value' => 'optional'],
    ['name' => 'blog', 'value' => 'enabled'],
    ['name' => 'recaptcha', 'value' => 'disabled'],
    ['name' => 'recaptcha_site_key', 'value' => ''],
    ['name' => 'recaptcha_secret_key', 'value' => ''],
    ['name' => 'livechat_script', 'value' => ''],
    ['name' => 'custom_js', 'value' => ''],
    ['name' => 'custom_css', 'value' => ''],
    ['name' => 'footer_text', 'value' => '© 2026 Trading Wallet. All rights reserved.'],
    ['name' => 'social_twitter', 'value' => 'https://twitter.com/tradingwallet'],
    ['name' => 'social_facebook', 'value' => 'https://facebook.com/tradingwallet'],
    ['name' => 'social_telegram', 'value' => 'https://t.me/tradingwallet'],
    ['name' => 'social_instagram', 'value' => 'https://instagram.com/tradingwallet'],
    ['name' => 'social_youtube', 'value' => 'https://youtube.com/@tradingwallet'],
    ['name' => 'social_linkedin', 'value' => 'https://linkedin.com/company/tradingwallet'],
    ['name' => 'social_reddit', 'value' => 'https://reddit.com/r/tradingwallet'],
    ['name' => 'social_discord', 'value' => 'https://discord.gg/tradingwallet'],
    ['name' => 'trade_min_amount', 'value' => '10'],
    ['name' => 'trade_max_amount', 'value' => '100000'],
    ['name' => 'withdrawal_min', 'value' => '10'],
    ['name' => 'withdrawal_max', 'value' => '50000'],
    ['name' => 'withdrawal_fee', 'value' => '1'],
    ['name' => 'withdrawal_fee_type', 'value' => 'percent'],
    ['name' => 'deposit_min', 'value' => '10'],
    ['name' => 'deposit_max', 'value' => '100000'],
    ['name' => 'transfer_min', 'value' => '1'],
    ['name' => 'transfer_max', 'value' => '50000'],
    ['name' => 'transfer_fee', 'value' => '1'],
    ['name' => 'transfer_fee_type', 'value' => 'percent'],
    ['name' => 'loan_min', 'value' => '100'],
    ['name' => 'loan_max', 'value' => '50000'],
    ['name' => 'investment_min', 'value' => '10'],
    ['name' => 'investment_max', 'value' => '100000'],
    ['name' => 'staking_min', 'value' => '10'],
    ['name' => 'staking_max', 'value' => '100000'],
];

foreach ($settings as $setting) {
    WebsiteSetting::firstOrCreate(
        ['name' => $setting['name']],
        ['value' => $setting['value']]
    );
}
echo "Inserted " . count($settings) . " website settings\n";

// Create admin user
$admin = User::firstOrCreate(
    ['email' => 'admin@trading-wallet.net'],
    [
        'name' => 'Super Admin',
        'username' => 'admin',
        'account_id' => 'ADM' . strtoupper(Str::random(8)),
        'password' => Hash::make('admin12345'),
        'account_bal' => 1000000,
        'role' => 'admin',
        'id_verified' => 'verified',
        'status' => 'active',
    ]
);
echo "Admin user: " . ($admin->wasRecentlyCreated ? "created" : "exists") . " - ID: {$admin->id}\n";

// Create test user
$user = User::firstOrCreate(
    ['email' => 'user@trading-wallet.net'],
    [
        'name' => 'Test User',
        'username' => 'testuser',
        'account_id' => 'USR' . strtoupper(Str::random(8)),
        'password' => Hash::make('user12345'),
        'account_bal' => 50000,
        'role' => 'user',
        'id_verified' => 'verified',
        'status' => 'active',
    ]
);
echo "Test user: " . ($user->wasRecentlyCreated ? "created" : "exists") . " - ID: {$user->id}\n";

// Verify theme setting
$theme = WebsiteSetting::where('name', 'theme')->first();
echo "\nTheme set to: " . ($theme ? $theme->value : 'NOT SET') . "\n";

// Check theme view exists
$viewPath = base_path("resources/views/themes/{$theme->value}/front/home.blade.php");
echo "Home view exists: " . (file_exists($viewPath) ? "YES" : "NO") . " at $viewPath\n";

// List theme files
$themeDir = base_path("resources/views/themes/{$theme->value}");
if (is_dir($themeDir)) {
    echo "\nTheme directories in {$theme->value}:\n";
    $items = scandir($themeDir);
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') echo "  $item\n";
    }
}
