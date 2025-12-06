<?php
require_once __DIR__ . '/../config/db.php';

echo "Checking evenements table structure:\n";
echo "====================================\n\n";

try {
    $stmt = $pdo->query("DESCRIBE evenements");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Columns in 'evenements' table:\n";
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
