<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Website Settings ===\n";
$settings = DB::table('website_settings')->get();
foreach ($settings as $s) {
    echo "  {$s->name} = " . substr($s->value, 0, 100) . "\n";
}

echo "\n=== Theme directories ===\n";
$themePath = base_path('resources/views/themes');
if (is_dir($themePath)) {
    $dirs = scandir($themePath);
    foreach ($dirs as $d) {
        if ($d != '.' && $d != '..') echo "  $d\n";
    }
} else {
    echo "  themes directory not found at: $themePath\n";
}

echo "\n=== Config credcrypto ===\n";
print_r(config('credcrypto'));
