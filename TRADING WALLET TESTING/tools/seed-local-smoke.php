<?php

$core = dirname(__DIR__, 2) . '/public_html/trading-wallet.net/core';
chdir($core);

require $core . '/vendor/autoload.php';

$app = require $core . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$now = date('Y-m-d H:i:s');

$settings = [
    'website_name' => 'Trading Wallet Local',
    'website_email' => 'local@example.test',
    'website_phone_no' => '+10000000000',
    'website_contact_address' => 'Local development environment',
    'theme' => 'cryptic',
    'main_css' => '',
    'google_captcha' => 'disabled',
    'livechat' => 'disabled',
    'email_verification' => 'disabled',
    'login_otp_user' => 'disabled',
    'login_otp_admin' => 'disabled',
    'id_verification' => 'disabled',
    'trader_mode' => 'enabled',
    'balance_transfer' => 'disabled',
    'loan' => 'disabled',
    'withdrawal_otp' => 'disabled',
    'withdrawal_fee_type' => 'fixed',
    'withdrawal_fee' => '0',
    'min_withdrawal' => '1',
    'max_withdrawal' => '100000',
    'min_transfer' => '1',
    'max_transfer' => '100000',
    'transfer_fee_type' => 'fixed',
    'transfer_fee' => '0',
    'transfer_auto_approve' => 'disabled',
    'general_currency' => 'USD',
    'decimal_places' => '2',
    'ref_bonus' => '0',
    'cron' => (string) time(),
    'custom_js' => '""',
    'whatsapp' => json_encode([
        'status' => 'disabled',
        'message' => 'Local testing only',
    ]),
    'meta' => json_encode([
        'description' => 'Local Trading Wallet testing environment',
        'keywords' => 'trading,wallet,local,test',
        'robots' => 'noindex,nofollow',
        'logo' => 'logo.png',
        'logo_rec' => 'logo-rec.png',
        'banner' => 'banner.jpg',
        'favicon' => 'favicon.png',
    ]),
];

foreach ($settings as $name => $value) {
    Illuminate\Support\Facades\DB::table('website_settings')->updateOrInsert(
        ['name' => $name],
        ['value' => $value, 'created_at' => $now, 'updated_at' => $now]
    );
}

Illuminate\Support\Facades\DB::table('sections')->updateOrInsert(
    ['name' => 'hero'],
    [
        'content' => json_encode([
            'section_heading' => 'Local Trading Wallet Testing',
            'section_text' => 'This browser session is running from the restored backup on a local development stack.',
            'section_button_text' => 'Open Login',
            'section_button_url' => '/login',
            'section_bg_img' => 'hero-bg-1662457559.jpg',
        ]),
        'pages' => json_encode(['home']),
        'created_at' => $now,
        'updated_at' => $now,
    ]
);

echo 'SMOKE_ROWS_READY settings='
    . Illuminate\Support\Facades\DB::table('website_settings')->count()
    . ' sections='
    . Illuminate\Support\Facades\DB::table('sections')->count()
    . PHP_EOL;
