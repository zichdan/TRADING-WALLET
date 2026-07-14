<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WebsiteSetting;
use Nwidart\Modules\Facades\Module;

// Add trader_mode setting
WebsiteSetting::firstOrCreate(
    ['name' => 'trader_mode'],
    ['value' => 'enabled']
);
echo "Added trader_mode=enabled\n";

// Enable all modules
$modules = Module::all();
echo "\n=== Modules found ===\n";
foreach ($modules as $name => $module) {
    echo "  $name: " . ($module->isEnabled() ? "enabled" : "disabled") . "\n";
    if (!$module->isEnabled()) {
        $module->enable();
        echo "  -> Enabled $name\n";
    }
}

// Verify
echo "\n=== Enabled modules ===\n";
$enabled = Module::allEnabled();
foreach ($enabled as $mod) {
    echo "  " . $mod->getName() . "\n";
}

echo "\ntrader_mode: " . DB::table('website_settings')->where('name', 'trader_mode')->value('value') . "\n";
echo "isAddonEnabled('cryptotrading'): " . (isAddonEnabled('cryptotrading') ? 'true' : 'false') . "\n";
