<?php

// Test account creation script
// Run with: php create_test_accounts.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Update admin password
$admin = Admin::find(1);
if ($admin) {
    $admin->password = Hash::make('Admin@2026!');
    $admin->save();
    echo "ADMIN ACCOUNT:\n";
    echo "  Email: " . $admin->email . "\n";
    echo "  Password: Admin@2026!\n";
    echo "  Status: Updated successfully\n\n";
} else {
    echo "Admin not found!\n";
}

// Create fresh test user
$existingUser = User::where('email', 'testuser@trading-wallet.net')->first();
if ($existingUser) {
    $existingUser->password = Hash::make('User@2026!');
    $existingUser->email_verified = 'verified';
    $existingUser->id_verified = 'verified';
    $existingUser->status = 'active';
    $existingUser->save();
    echo "USER ACCOUNT (existing updated):\n";
    echo "  Email: " . $existingUser->email . "\n";
    echo "  Password: User@2026!\n";
} else {
    $user = new User();
    $user->first_name = 'Test';
    $user->last_name = 'Trader';
    $user->email = 'testuser@trading-wallet.net';
    $user->account_id = 'TW' . rand(1000, 9999);
    $user->password = Hash::make('User@2026!');
    $user->phone_no = '1234567890';
    $user->account_bal = 0;
    $user->email_verified = 'verified';
    $user->id_verified = 'verified';
    $user->status = 'active';
    $user->country = 'United States';
    $user->dob = '1990-01-01';
    $user->gender = 'Male';
    $user->street_address = '123 Test St';
    $user->state = 'CA';
    $user->save();

    echo "USER ACCOUNT (newly created):\n";
    echo "  Email: " . $user->email . "\n";
    echo "  Password: User@2026!\n";
}

// Ensure email_verification and id_verification are disabled
$settings = DB::table('website_settings')->whereIn('name', ['email_verification', 'id_verification', 'google_captcha'])->get();
foreach ($settings as $setting) {
    echo "Setting: " . $setting->name . " = " . $setting->value . "\n";
}

// Set email_verification to disabled if not already
$dbCheck = DB::table('website_settings')->where('name', 'email_verification')->first();
if ($dbCheck) {
    DB::table('website_settings')->where('name', 'email_verification')->update(['value' => 'disabled']);
    echo "email_verification set to: disabled\n";
} else {
    DB::table('website_settings')->insert(['name' => 'email_verification', 'value' => 'disabled']);
    echo "email_verification created and set to: disabled\n";
}

// Set id_verification to disabled
$dbCheck = DB::table('website_settings')->where('name', 'id_verification')->first();
if ($dbCheck) {
    DB::table('website_settings')->where('name', 'id_verification')->update(['value' => 'disabled']);
    echo "id_verification set to: disabled\n";
} else {
    DB::table('website_settings')->insert(['name' => 'id_verification', 'value' => 'disabled']);
    echo "id_verification created and set to: disabled\n";
}

// Set google_captcha to disabled
$dbCheck = DB::table('website_settings')->where('name', 'google_captcha')->first();
if ($dbCheck) {
    DB::table('website_settings')->where('name', 'google_captcha')->update(['value' => 'disabled']);
    echo "google_captcha set to: disabled\n";
} else {
    DB::table('website_settings')->insert(['name' => 'google_captcha', 'value' => 'disabled']);
    echo "google_captcha created and set to: disabled\n";
}

echo "\nDone! Test accounts are ready.\n";
