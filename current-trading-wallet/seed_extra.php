<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WebsiteSetting;

// Add whatsapp JSON setting
$whatsapp = json_encode([
    'status' => 'disabled',
    'number' => '',
    'message' => '',
]);

WebsiteSetting::firstOrCreate(
    ['name' => 'whatsapp'],
    ['value' => $whatsapp]
);
echo "Added 'whatsapp' setting\n";

// Add other potentially needed JSON settings
$tawkto = json_encode([
    'status' => 'disabled',
    'property_id' => '',
    'widget_id' => '',
]);
WebsiteSetting::firstOrCreate(
    ['name' => 'tawkto'],
    ['value' => $tawkto]
);
echo "Added 'tawkto' setting\n";

$firebase = json_encode([
    'status' => 'disabled',
    'api_key' => '',
    'auth_domain' => '',
    'project_id' => '',
    'storage_bucket' => '',
    'messaging_sender_id' => '',
    'app_id' => '',
]);
WebsiteSetting::firstOrCreate(
    ['name' => 'firebase'],
    ['value' => $firebase]
);
echo "Added 'firebase' setting\n";

// Add trade settings
$tradeSettings = [
    ['name' => 'trade_status', 'value' => 'enabled'],
    ['name' => 'trade_fee', 'value' => '0.1'],
    ['name' => 'trade_fee_type', 'value' => 'percent'],
    ['name' => 'trade_leverage', 'value' => 'enabled'],
    ['name' => 'trade_leverage_max', 'value' => '100'],
    ['name' => 'demo_trading', 'value' => 'enabled'],
    ['name' => 'staking_status', 'value' => 'enabled'],
    ['name' => 'signal_status', 'value' => 'enabled'],
    ['name' => 'bot_trading', 'value' => 'enabled'],
    ['name' => 'loan_status', 'value' => 'enabled'],
    ['name' => 'investment_status', 'value' => 'enabled'],
    ['name' => 'p2p_transfer', 'value' => 'enabled'],
    ['name' => 'kyc_status', 'value' => 'optional'],
    ['name' => 'two_fa', 'value' => 'disabled'],
    ['name' => 'otp_login', 'value' => 'disabled'],
    ['name' => 'affiliate', 'value' => 'enabled'],
    ['name' => 'affiliate_commission', 'value' => '5'],
    ['name' => 'affiliate_commission_type', 'value' => 'percent'],
    ['name' => 'language', 'value' => 'en'],
    ['name' => 'timezone', 'value' => 'UTC'],
    ['name' => 'site_status', 'value' => 'live'],
    ['name' => 'cookie_consent', 'value' => 'disabled'],
    ['name' => 'dark_mode', 'value' => 'enabled'],
    ['name' => 'maintenance_message', 'value' => 'We are under maintenance. Please check back soon.'],
];

foreach ($tradeSettings as $setting) {
    WebsiteSetting::firstOrCreate(
        ['name' => $setting['name']],
        ['value' => $setting['value']]
    );
}
echo "Added " . count($tradeSettings) . " trade/feature settings\n";

echo "\nTotal settings: " . DB::table('website_settings')->count() . "\n";
