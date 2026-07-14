<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WebsiteSetting;

// Check current whatsapp setting
$current = WebsiteSetting::where('name', 'whatsapp')->first();
echo "Current whatsapp setting: " . ($current ? $current->value : 'NOT SET') . "\n";

// Set proper whatsapp JSON
$whatsapp = json_encode([
    'status' => 'disabled',
    'no' => '1234567890',
    'message' => 'Hello, I need help with my account.'
]);

WebsiteSetting::updateOrCreate(
    ['name' => 'whatsapp'],
    ['value' => $whatsapp]
);
echo "Updated whatsapp setting to: $whatsapp\n";

// Also check tawkto
$tawkto = WebsiteSetting::where('name', 'tawkto')->first();
echo "\nCurrent tawkto setting: " . ($tawkto ? $tawkto->value : 'NOT SET') . "\n";

if (!$tawkto || empty($tawkto->value)) {
    WebsiteSetting::updateOrCreate(
        ['name' => 'tawkto'],
        ['value' => json_encode(['status' => 'disabled', 'property_id' => '', 'widget_id' => ''])]
    );
    echo "Updated tawkto setting\n";
}

// Verify
$whatsapp = WebsiteSetting::where('name', 'whatsapp')->first();
echo "\nVerified whatsapp: " . $whatsapp->value . "\n";
$decoded = json_decode($whatsapp->value);
echo "Decoded: no=" . $decoded->no . " status=" . $decoded->status . " message=" . $decoded->message . "\n";
