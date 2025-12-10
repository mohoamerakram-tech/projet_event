<?php
require_once __DIR__ . '/config/db.php';
$db = new Database();
$pdo = $db->getConnection();

try {
    echo "Attempting to fix notifications table...\n";
    // First, let's see if we can just modify it
    $sql = "ALTER TABLE notifications MODIFY id INT AUTO_INCREMENT";
    $pdo->exec($sql);
    echo "Success: notifications table id is now AUTO_INCREMENT.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
