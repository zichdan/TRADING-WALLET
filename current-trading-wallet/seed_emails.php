<?php
require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check email_templates table structure
$cols = DB::select("DESCRIBE email_templates");
echo "=== email_templates columns ===\n";
foreach ($cols as $col) {
    echo "  {$col->Field} | {$col->Type} | Null: {$col->Null} | Default: " . ($col->Default ?? 'NONE') . "\n";
}

echo "\nExisting templates: " . DB::table('email_templates')->count() . "\n";

// Insert essential email templates
$templates = [
    [
        'name' => 'welcome_mail',
        'subject' => 'Welcome to {{ $website_name }}',
        'body' => '<h2>Welcome to {{ $website_name }}</h2><p>Dear {{ $first_name }} {{ $last_name }},</p><p>Your account has been created successfully. Welcome to our trading platform!</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'new_referral_mail',
        'subject' => 'New Referral on {{ $website_name }}',
        'body' => '<h2>New Referral</h2><p>Dear {{ $first_name }} {{ $last_name }},</p><p>A new user {{ $name }} ({{ $email }}) has registered using your referral link.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'password_reset_mail',
        'subject' => 'Password Reset - {{ $website_name }}',
        'body' => '<h2>Password Reset</h2><p>Dear user,</p><p>You requested a password reset. Click the link below to reset your password:</p><p><a href="{{ $reset_link }}">Reset Password</a></p><p>If you did not request this, please ignore this email.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'deposit_mail',
        'subject' => 'Deposit Confirmation - {{ $website_name }}',
        'body' => '<h2>Deposit Confirmation</h2><p>Dear {{ $first_name }},</p><p>Your deposit of {{ $amount }} has been confirmed.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'withdrawal_mail',
        'subject' => 'Withdrawal Confirmation - {{ $website_name }}',
        'body' => '<h2>Withdrawal Confirmation</h2><p>Dear {{ $first_name }},</p><p>Your withdrawal of {{ $amount }} has been processed.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'transfer_mail',
        'subject' => 'Transfer Confirmation - {{ $website_name }}',
        'body' => '<h2>Transfer Confirmation</h2><p>Dear {{ $first_name }},</p><p>Your transfer of {{ $amount }} to {{ $recipient }} has been processed.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'trade_mail',
        'subject' => 'Trade Confirmation - {{ $website_name }}',
        'body' => '<h2>Trade Confirmation</h2><p>Dear {{ $first_name }},</p><p>Your trade has been executed successfully.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'loan_processed_mail',
        'subject' => 'Loan Update - {{ $website_name }}',
        'body' => '<h2>Loan Update</h2><p>Dear user,</p><p>Your loan request has been processed.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'id_processed_mail',
        'subject' => 'ID Verification Update - {{ $website_name }}',
        'body' => '<h2>ID Verification Update</h2><p>Dear user,</p><p>Your ID verification status has been updated.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'verification_mail',
        'subject' => 'Email Verification - {{ $website_name }}',
        'body' => '<h2>Email Verification</h2><p>Dear user,</p><p>Please verify your email by clicking the link below:</p><p><a href="{{ $verification_link }}">Verify Email</a></p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'ticket_reply_mail',
        'subject' => 'Support Ticket Reply - {{ $website_name }}',
        'body' => '<h2>Support Ticket Reply</h2><p>Dear user,</p><p>Your support ticket has received a reply.</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
    [
        'name' => 'welcome_bonus_mail',
        'subject' => 'Welcome Bonus - {{ $website_name }}',
        'body' => '<h2>Welcome Bonus</h2><p>Dear user,</p><p>You have received a welcome bonus!</p><p>Best regards,<br>{{ $website_name }} Team</p>',
        'status' => 'enabled',
    ],
];

$now = now();
foreach ($templates as $tpl) {
    $existing = DB::table('email_templates')->where('name', $tpl['name'])->first();
    if (!$existing) {
        DB::table('email_templates')->insert(array_merge($tpl, ['created_at' => $now, 'updated_at' => $now]));
        echo "Inserted: {$tpl['name']}\n";
    } else {
        echo "Exists: {$tpl['name']}\n";
    }
}

echo "\nTotal email templates: " . DB::table('email_templates')->count() . "\n";
