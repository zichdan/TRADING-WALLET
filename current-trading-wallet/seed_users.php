<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Create admin user via direct DB insert
$adminExists = DB::table('users')->where('email', 'admin@trading-wallet.net')->exists();
if (!$adminExists) {
    DB::table('users')->insert([
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => 'admin@trading-wallet.net',
        'account_id' => 'ADM' . strtoupper(Str::random(8)),
        'password' => Hash::make('admin12345'),
        'phone_no' => '+1234567890',
        'account_bal' => 1000000,
        'email_verified' => 'verified',
        'id_verified' => 'verified',
        'status' => 'active',
        'street_address' => '123 Admin Street',
        'state' => 'Admin State',
        'country' => 'United States',
        'tcal' => '0',
        'profile_picture' => 'default.png',
        'g2fa' => 'disabled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Admin user created\n";
} else {
    echo "Admin user already exists\n";
}

// Create test user
$userExists = DB::table('users')->where('email', 'user@trading-wallet.net')->exists();
if (!$userExists) {
    DB::table('users')->insert([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'user@trading-wallet.net',
        'account_id' => 'USR' . strtoupper(Str::random(8)),
        'password' => Hash::make('user12345'),
        'phone_no' => '+1234567891',
        'account_bal' => 50000,
        'email_verified' => 'verified',
        'id_verified' => 'verified',
        'status' => 'active',
        'street_address' => '456 User Avenue',
        'state' => 'User State',
        'country' => 'United States',
        'tcal' => '0',
        'profile_picture' => 'default.png',
        'g2fa' => 'disabled',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Test user created\n";
} else {
    echo "Test user already exists\n";
}

// Check admins table
try {
    $adminCount = DB::table('admins')->count();
    if ($adminCount == 0) {
        $adminCols = DB::select("DESCRIBE admins");
        $adminData = [];
        foreach ($adminCols as $col) {
            if (in_array($col->Field, ['id', 'created_at', 'updated_at', 'remember_token'])) continue;
            if ($col->Null == 'NO' && $col->Default === null) {
                if ($col->Field == 'first_name') $adminData[$col->Field] = 'Super';
                elseif ($col->Field == 'last_name') $adminData[$col->Field] = 'Admin';
                elseif ($col->Field == 'email') $adminData[$col->Field] = 'admin@trading-wallet.net';
                elseif ($col->Field == 'password') $adminData[$col->Field] = Hash::make('admin12345');
                else $adminData[$col->Field] = '0';
            }
        }
        $adminData['created_at'] = now();
        $adminData['updated_at'] = now();
        DB::table('admins')->insert($adminData);
        echo "Admin record created in admins table\n";
    } else {
        echo "Admins table has $adminCount records\n";
    }
} catch (Exception $e) {
    echo "Admins table error: " . $e->getMessage() . "\n";
}

// Verify
echo "\n=== Verification ===\n";
echo "Website settings count: " . DB::table('website_settings')->count() . "\n";
echo "Users count: " . DB::table('users')->count() . "\n";
echo "Admins count: " . DB::table('admins')->count() . "\n";
echo "Theme: " . DB::table('website_settings')->where('name', 'theme')->value('value') . "\n";

// Check theme view
$theme = DB::table('website_settings')->where('name', 'theme')->value('value');
$viewPath = base_path("resources/views/themes/{$theme}/front/home.blade.php");
echo "Home view exists: " . (file_exists($viewPath) ? "YES" : "NO") . " at $viewPath\n";

if (!file_exists($viewPath)) {
    echo "\nSearching for home view in theme...\n";
    $themeDir = base_path("resources/views/themes/{$theme}");
    if (is_dir($themeDir)) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($themeDir));
        foreach ($it as $file) {
            if (strpos($file->getFilename(), 'home') !== false) {
                echo "  Found: " . $file->getPathname() . "\n";
            }
        }
    }
}
