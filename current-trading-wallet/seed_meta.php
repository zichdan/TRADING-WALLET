<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WebsiteSetting;

// Add meta JSON setting
$meta = json_encode([
    'description' => 'Trading Wallet - Secure cryptocurrency trading platform',
    'keywords' => 'crypto, trading, wallet, bitcoin, ethereum, usdt, exchange',
    'banner' => 'banner.png',
    'robots' => 'index, follow',
    'favicon' => 'favicon.png',
    'logo' => 'logo.png',
    'logo_rec' => 'logo-rec.png',
]);

WebsiteSetting::firstOrCreate(
    ['name' => 'meta'],
    ['value' => $meta]
);
echo "Added 'meta' setting\n";

// Add website_name (used by theme instead of site_name)
WebsiteSetting::firstOrCreate(
    ['name' => 'website_name'],
    ['value' => 'Trading Wallet']
);
echo "Added 'website_name' setting\n";

// Add other commonly needed settings
$extraSettings = [
    ['name' => 'preloader', 'value' => 'enabled'],
    ['name' => 'announcement_bar', 'value' => 'disabled'],
    ['name' => 'announcement_text', 'value' => ''],
    ['name' => 'currency_position', 'value' => 'left'],
    ['name' => 'thousand_separator', 'value' => ','],
    ['name' => 'decimal_separator', 'value' => '.'],
    ['name' => 'site_url', 'value' => 'http://localhost:8000'],
    ['name' => 'site_logo_dark', 'value' => 'logo.png'],
    ['name' => 'site_logo_light', 'value' => 'logo.png'],
    ['name' => 'login_recaptcha', 'value' => 'disabled'],
    ['name' => 'register_recaptcha', 'value' => 'disabled'],
    ['name' => 'front_recaptcha', 'value' => 'disabled'],
    ['name' => 'user_theme', 'value' => 'prius'],
    ['name' => 'admin_theme', 'value' => 'prius'],
];

foreach ($extraSettings as $setting) {
    WebsiteSetting::firstOrCreate(
        ['name' => $setting['name']],
        ['value' => $setting['value']]
    );
}
echo "Added " . count($extraSettings) . " extra settings\n";

// Verify
echo "\nTotal settings: " . DB::table('website_settings')->count() . "\n";
echo "meta: " . DB::table('website_settings')->where('name', 'meta')->value('value') . "\n";
echo "website_name: " . DB::table('website_settings')->where('name', 'website_name')->value('value') . "\n";
echo "theme: " . DB::table('website_settings')->where('name', 'theme')->value('value') . "\n";
