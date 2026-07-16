<?php
/*
 * TradingWallet Artisan Runner - Web-based
 * Use this when you don't have SSH/shell access
 * Upload to public_html/ and visit in browser
 * DELETE THIS FILE after setup is complete
 */

$password = 'ChangeThisPassword2026';

if (!isset($_POST['password']) || $_POST['password'] !== $password) {
    die('<!DOCTYPE html><html><head><title>Artisan Runner</title>
    <style>body{font-family:monospace;background:#1a1a2e;color:#eee;padding:40px;max-width:800px;margin:0 auto}
    form{background:#16213e;padding:30px;border-radius:8px}
    input{padding:10px;margin:5px;width:100%;box-sizing:border-box}
    button{padding:10px 20px;background:#0f3460;color:#eee;border:none;border-radius:4px;cursor:pointer}
    button:hover{background:#e94560}
    h2{color:#e94560}</style></head>
    <body><form method="POST">
    <h2>TradingWallet Artisan Runner</h2>
    <p>Enter password to run artisan commands:</p>
    <input type="password" name="password" placeholder="Password" autofocus>
    <button type="submit">Login</button>
    </form></body></html>');
}

$allowed = [
    'migrate --force',
    'migrate:status',
    'migrate:rollback --force',
    'migrate:fresh --force',
    'migrate:fresh --seed --force',
    'config:cache',
    'config:clear',
    'route:cache',
    'route:clear',
    'view:cache',
    'view:clear',
    'optimize',
    'optimize:clear',
    'storage:link',
    'key:generate',
    'db:seed --force',
    'db:show',
    'db:table --table=users',
    'db:table --table=trading_logs',
    'db:table --table=deposits',
    'cache:clear',
    'queue:restart',
    'up',
    'down',
    'about',
    'list',
    'tinker'
];

$command = isset($_POST['cmd']) ? trim($_POST['cmd']) : 'about';

if (!in_array($command, $allowed)) {
    $command = 'about';
}

$corePath = __DIR__ . '/core';
chdir($corePath);

$phpPath = '/usr/local/bin/php';
if (!file_exists($phpPath)) {
    $phpPath = 'php';
}

$fullCommand = escapeshellarg($phpPath) . ' artisan ' . $command . ' 2>&1';
$output = shell_exec($fullCommand);

?>
<!DOCTYPE html>
<html><head><title>Artisan Runner</title>
<style>
body{font-family:monospace;background:#1a1a2e;color:#eee;padding:20px;max-width:900px;margin:0 auto}
.header{background:#16213e;padding:20px;border-radius:8px;margin-bottom:20px}
.output{background:#0f0f1e;padding:20px;border-radius:8px;border:1px solid #333;white-space:pre-wrap;word-wrap:break-word;max-height:500px;overflow-y:auto;margin-bottom:20px}
form{background:#16213e;padding:20px;border-radius:8px;margin-bottom:20px}
select,input{padding:8px;margin:5px}
button{padding:10px 20px;background:#0f3460;color:#eee;border:none;border-radius:4px;cursor:pointer}
button:hover{background:#e94560}
.cmd-btn{display:inline-block;margin:3px;padding:5px 10px;background:#0f3460;color:#eee;border:none;border-radius:3px;cursor:pointer;font-size:12px}
.cmd-btn:hover{background:#e94560}
h2{color:#e94560}
.warning{color:#e94560;font-weight:bold}
.ok{color:#0f0}
</style></head>
<body>
<div class="header">
<h2>TradingWallet Artisan Runner</h2>
<p>Working directory: <?php echo htmlspecialchars($corePath); ?></p>
<p>PHP path: <?php echo htmlspecialchars($phpPath); ?></p>
<p>vendor/autoload.php: <?php echo file_exists($corePath . '/vendor/autoload.php') ? '<span class="ok">EXISTS</span>' : '<span class="warning">MISSING - run composer install first!</span>'; ?></p>
<p>.env file: <?php echo file_exists($corePath . '/.env') ? '<span class="ok">EXISTS</span>' : '<span class="warning">MISSING - create .env first!</span>'; ?></p>
</div>

<form method="POST">
<input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
<label>Custom command: php artisan </label>
<input type="text" name="cmd" value="<?php echo htmlspecialchars($command); ?>" style="width:300px">
<button type="submit">Run</button>
</form>

<form method="POST">
<input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
<p><strong>Quick commands:</strong></p>
<?php foreach ($allowed as $cmd): ?>
<button type="submit" name="cmd" value="<?php echo htmlspecialchars($cmd); ?>" class="cmd-btn"><?php echo htmlspecialchars($cmd); ?></button>
<?php endforeach; ?>
</form>

<div class="output">
<strong>Output of: php artisan <?php echo htmlspecialchars($command); ?></strong>

<?php echo htmlspecialchars($output ?? 'No output'); ?>
</div>

<p class="warning">WARNING: Delete this file (artisan-runner.php) after setup is complete!</p>
</body></html>
