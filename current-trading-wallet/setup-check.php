<?php
/*
 * TradingWallet Setup Checker
 * Upload to public_html/ and visit in browser
 * Checks all server requirements for TradingWallet
 * DELETE THIS FILE after setup is complete
 */

$checks = [];
$allOk = true;

$phpVersion = phpversion();
$checks['php_version'] = [
    'label' => 'PHP Version',
    'value' => $phpVersion,
    'required' => '8.1+',
    'status' => version_compare($phpVersion, '8.1.0', '>=') ? 'ok' : 'fail'
];

$requiredExtensions = ['mbstring', 'openssl', 'pdo', 'pdo_mysql', 'tokenizer', 'xml', 'cType', 'JSON', 'bcmath', 'curl', 'gd', 'fileinfo', 'ioncube_loader'];
foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    $checks['ext_' . $ext] = [
        'label' => 'Extension: ' . $ext,
        'value' => $loaded ? 'Loaded' : 'NOT Loaded',
        'required' => 'Required',
        'status' => $loaded ? 'ok' : 'fail'
    ];
    if (!$loaded && $ext !== 'ioncube_loader') {
        $allOk = false;
    }
}

$corePath = __DIR__ . '/core';
$vendorExists = file_exists($corePath . '/vendor/autoload.php');
$checks['vendor'] = [
    'label' => 'Composer Vendor (core/vendor/autoload.php)',
    'value' => $vendorExists ? 'EXISTS' : 'MISSING - Need to upload vendor/ or run composer install',
    'required' => 'Required for app to work',
    'status' => $vendorExists ? 'ok' : 'fail'
];
if (!$vendorExists) {
    $allOk = false;
}

$envExists = file_exists($corePath . '/.env');
$checks['env'] = [
    'label' => 'Environment File (core/.env)',
    'value' => $envExists ? 'EXISTS' : 'MISSING - Need to create .env from .env.example',
    'required' => 'Required for app to work',
    'status' => $envExists ? 'ok' : 'fail'
];
if (!$envExists) {
    $allOk = false;
}

$artisanExists = file_exists($corePath . '/artisan');
$checks['artisan'] = [
    'label' => 'Artisan CLI (core/artisan)',
    'value' => $artisanExists ? 'EXISTS' : 'MISSING',
    'required' => 'Required for migrations',
    'status' => $artisanExists ? 'ok' : 'fail'
];

$storagePath = $corePath . '/storage';
$storageWritable = is_writable($storagePath);
$checks['storage'] = [
    'label' => 'Storage Writable (core/storage)',
    'value' => $storageWritable ? 'Writable' : 'NOT Writable - chmod 755',
    'required' => 'Required for logs, cache, sessions',
    'status' => $storageWritable ? 'ok' : 'fail'
];

$bootstrapCachePath = $corePath . '/bootstrap/cache';
$bootstrapWritable = is_writable($bootstrapCachePath);
$checks['bootstrap'] = [
    'label' => 'Bootstrap Cache Writable (core/bootstrap/cache)',
    'value' => $bootstrapWritable ? 'Writable' : 'NOT Writable - chmod 755',
    'required' => 'Required for config/route/view cache',
    'status' => $bootstrapWritable ? 'ok' : 'fail'
];

$publicStorage = __DIR__ . '/public/storage';
$publicStorageExists = file_exists($publicStorage) || is_link($publicStorage);
$checks['public_storage'] = [
    'label' => 'Public Storage Symlink (public/storage)',
    'value' => $publicStorageExists ? 'EXISTS' : 'MISSING - run php artisan storage:link',
    'required' => 'For file uploads to be accessible',
    'status' => $publicStorageExists ? 'ok' : 'warn'
];

$installedFile = $corePath . '/app/installed.txt';
$installedExists = file_exists($installedFile);
$checks['installed'] = [
    'label' => 'Installation Marker (core/app/installed.txt)',
    'value' => $installedExists ? 'INSTALLED' : 'NOT INSTALLED - app will redirect to /install',
    'required' => 'Required to skip installer',
    'status' => $installedExists ? 'ok' : 'warn'
];

$shellExecEnabled = function_exists('shell_exec');
$checks['shell_exec'] = [
    'label' => 'shell_exec() Available',
    'value' => $shellExecEnabled ? 'Available' : 'Disabled - artisan-runner.php will not work',
    'required' => 'Needed for web-based artisan commands',
    'status' => $shellExecEnabled ? 'ok' : 'fail'
];

$phpPath = '/usr/local/bin/php';
$phpPathExists = file_exists($phpPath);
$checks['php_path'] = [
    'label' => 'PHP CLI Path (/usr/local/bin/php)',
    'value' => $phpPathExists ? 'EXISTS' : 'NOT FOUND - will try default php',
    'required' => 'For running artisan commands',
    'status' => $phpPathExists ? 'ok' : 'warn'
];

if ($envExists) {
    $envContent = file_get_contents($corePath . '/.env');
    preg_match('/DB_HOST="?([^"\n]+)"?/', $envContent, $dbHost);
    preg_match('/DB_DATABASE="?([^"\n]+)"?/', $envContent, $dbName);
    preg_match('/DB_USERNAME="?([^"\n]+)"?/', $envContent, $dbUser);
    preg_match('/DB_PASSWORD="?([^"\n]*)"?\s*$/m', $envContent, $dbPass);

    $dbHostVal = $dbHost[1] ?? '127.0.0.1';
    $dbNameVal = $dbName[1] ?? 'unknown';
    $dbUserVal = $dbUser[1] ?? 'unknown';
    $dbPassVal = $dbPass[1] ?? '';

    try {
        $pdo = new PDO("mysql:host=$dbHostVal;dbname=$dbNameVal", $dbUserVal, $dbPassVal, [PDO::ATTR_TIMEOUT => 5]);
        $checks['database'] = [
            'label' => 'Database Connection',
            'value' => "Connected to $dbNameVal@$dbHostVal",
            'required' => 'Required for app to work',
            'status' => 'ok'
        ];

        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $checks['tables'] = [
            'label' => 'Database Tables',
            'value' => count($tables) . ' tables found',
            'required' => 'Need at least users table',
            'status' => count($tables) > 0 ? 'ok' : 'fail'
        ];

        if (count($tables) === 0) {
            $allOk = false;
        }
    } catch (Exception $e) {
        $checks['database'] = [
            'label' => 'Database Connection',
            'value' => 'FAILED: ' . $e->getMessage(),
            'required' => 'Required for app to work',
            'status' => 'fail'
        ];
        $allOk = false;
    }
}

?>
<!DOCTYPE html>
<html><head><title>TradingWallet Setup Checker</title>
<style>
body{font-family:monospace;background:#1a1a2e;color:#eee;padding:20px;max-width:900px;margin:0 auto}
.header{background:#16213e;padding:20px;border-radius:8px;margin-bottom:20px}
table{width:100%;border-collapse:collapse;background:#16213e;border-radius:8px;overflow:hidden}
th,td{padding:12px;text-align:left;border-bottom:1px solid #0f3460}
th{background:#0f3460;color:#e94560}
.ok{color:#0f0;font-weight:bold}
.fail{color:#e94560;font-weight:bold}
.warn{color:#ffa500;font-weight:bold}
h2{color:#e94560}
.summary{padding:20px;border-radius:8px;margin-top:20px;text-align:center;font-size:18px;font-weight:bold}
.summary.ok{background:#0a3d0a;color:#0f0}
.summary.fail{background:#3d0a0a;color:#e94560}
.instructions{background:#16213e;padding:20px;border-radius:8px;margin-top:20px}
.instructions h3{color:#e94560}
.instructions ol{padding-left:20px}
.instructions li{margin:10px 0}
.instructions code{background:#0f0f1e;padding:2px 6px;border-radius:3px;color:#e94560}
</style></head>
<body>
<div class="header">
<h2>TradingWallet Setup Checker</h2>
<p>Server: <?php echo htmlspecialchars($_SERVER['SERVER_NAME'] ?? 'unknown'); ?></p>
<p>Document Root: <?php echo htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'unknown'); ?></p>
<p>Core Path: <?php echo htmlspecialchars($corePath); ?></p>
</div>

<table>
<tr><th>Check</th><th>Value</th><th>Required</th><th>Status</th></tr>
<?php foreach ($checks as $check): ?>
<tr>
<td><?php echo htmlspecialchars($check['label']); ?></td>
<td><?php echo htmlspecialchars($check['value']); ?></td>
<td><?php echo htmlspecialchars($check['required']); ?></td>
<td class="<?php echo $check['status']; ?>"><?php echo strtoupper($check['status']); ?></td>
</tr>
<?php endforeach; ?>
</table>

<div class="summary <?php echo $allOk ? 'ok' : 'fail'; ?>">
<?php if ($allOk): ?>
ALL CHECKS PASSED - App should work!
<?php else: ?>
SOME CHECKS FAILED - Fix the issues below
<?php endif; ?>
</div>

<?php if (!$vendorExists): ?>
<div class="instructions">
<h3>How to fix MISSING VENDOR:</h3>
<ol>
<li>On your PC, open terminal in the <code>core/</code> directory of your project</li>
<li>Run: <code>composer install --no-dev --optimize-autoloader</code></li>
<li>Zip the <code>core/vendor/</code> folder as <code>vendor.zip</code></li>
<li>In cPanel, go to File Manager > public_html > core/</li>
<li>Upload <code>vendor.zip</code> and click Extract</li>
<li>Delete <code>vendor.zip</code> after extraction</li>
</ol>
</div>
<?php endif; ?>

<?php if (!$envExists): ?>
<div class="instructions">
<h3>How to fix MISSING .env:</h3>
<ol>
<li>In cPanel File Manager, go to <code>public_html/core/</code></li>
<li>Create a new file named <code>.env</code></li>
<li>Copy content from <code>.env.example</code> and update:
<ul>
<li><code>APP_ENV=production</code></li>
<li><code>APP_DEBUG=false</code></li>
<li><code>APP_URL=https://yourdomain.com</code></li>
<li><code>DB_DATABASE=your_cpanel_db_name</code></li>
<li><code>DB_USERNAME=your_cpanel_db_user</code></li>
<li><code>DB_PASSWORD=your_db_password</code></li>
</ul>
</li>
<li>Save the file</li>
</ol>
</div>
<?php endif; ?>

<?php if (isset($checks['tables']) && $checks['tables']['status'] === 'fail'): ?>
<div class="instructions">
<h3>How to fix MISSING TABLES (0 tables in database):</h3>
<ol>
<li>Visit <code>https://yourdomain.com/artisan-runner.php</code></li>
<li>Enter password and run <code>migrate --force</code></li>
<li>Or import your SQL file via phpMyAdmin</li>
</ol>
</div>
<?php endif; ?>

<p style="text-align:center;margin-top:20px;color:#e94560;">
WARNING: Delete this file (setup-check.php) after setup is complete!
</p>
</body></html>
