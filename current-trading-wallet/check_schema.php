<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Check users table columns
$cols = DB::select("DESCRIBE users");
echo "=== Users table columns ===\n";
foreach ($cols as $col) {
    echo "  {$col->Field} | {$col->Type} | Null: {$col->Null} | Default: " . ($col->Default ?? 'NONE') . "\n";
}
