<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=trading_wallet', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("ALTER TABLE users MODIFY COLUMN tcal VARCHAR(255) NOT NULL DEFAULT '0'");
echo "Updated tcal column\n";

$pdo->exec("ALTER TABLE users MODIFY COLUMN profile_picture VARCHAR(255) NOT NULL DEFAULT 'default.png'");
echo "Updated profile_picture column\n";

$pdo->exec("ALTER TABLE users MODIFY COLUMN g2fa VARCHAR(255) NOT NULL DEFAULT 'disabled'");
echo "Updated g2fa column\n";

$pdo->exec("ALTER TABLE users MODIFY COLUMN email_verified VARCHAR(255) NOT NULL DEFAULT 'pending'");
echo "Updated email_verified column\n";

$pdo->exec("ALTER TABLE users MODIFY COLUMN id_verified VARCHAR(255) NOT NULL DEFAULT 'pending'");
echo "Updated id_verified column\n";

$pdo->exec("ALTER TABLE users MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'active'");
echo "Updated status column\n";

echo "All columns updated successfully\n";
