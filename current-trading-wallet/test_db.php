<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "MySQL connected successfully\n";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS trading_wallet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database 'trading_wallet' created/verified\n";
    
    // Show databases
    $stmt = $pdo->query("SHOW DATABASES");
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Available databases: " . implode(', ', $dbs) . "\n";
} catch (Exception $e) {
    echo "MySQL error: " . $e->getMessage() . "\n";
}
