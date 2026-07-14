<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Direct DB update
$whatsapp = json_encode([
    'status' => 'disabled',
    'no' => '1234567890',
    'message' => 'Hello, I need help with my account.'
]);

DB::table('website_settings')->updateOrInsert(
    ['name' => 'whatsapp'],
    ['value' => $whatsapp]
);

// Verify
$row = DB::table('website_settings')->where('name', 'whatsapp')->first();
echo "whatsapp value: " . $row->value . "\n";
$decoded = json_decode($row->value);
echo "no=" . ($decoded->no ?? 'MISSING') . " status=" . ($decoded->status ?? 'MISSING') . "\n";

// Clear any cache
cache()->flush();
echo "Cache flushed\n";
