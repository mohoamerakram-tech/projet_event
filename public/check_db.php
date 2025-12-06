<?php
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'inscriptions'");
    if ($stmt->rowCount() > 0) {
        echo "Table 'inscriptions' exists.\n";
        $stmt = $pdo->query("DESCRIBE inscriptions");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo $col['Field'] . " - " . $col['Type'] . "\n";
        }
    } else {
        echo "Table 'inscriptions' does not exist.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
