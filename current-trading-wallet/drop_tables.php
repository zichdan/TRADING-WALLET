<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=trading_wallet', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Disable FK checks
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

// Drop all tables
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    $pdo->exec("DROP TABLE IF EXISTS `$table`");
    echo "Dropped: $table\n";
}

// Re-enable FK checks
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "All tables dropped. Ready for fresh migration.\n";
