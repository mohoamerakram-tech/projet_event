<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM evenements LIKE 'capacite'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE evenements ADD COLUMN capacite INT DEFAULT NULL");
        echo "Column 'capacite' added successfully.";
    } else {
        echo "Column 'capacite' already exists.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
