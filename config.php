<?php

define('DB_HOST', 'sql112.infinityfree.com');
define('DB_NAME', 'if0_39527595_news_site');
define('DB_USER', 'if0_39527595');
define('DB_PASS', 'e7Sb3VBX4a3Cv');

$charset = 'utf8mb4';
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=$charset";

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>
