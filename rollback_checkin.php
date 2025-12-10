<?php
/**
 * Rollback Script: Remove Check-In Columns from Inscriptions Table
 */

require_once 'config/db.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    echo "=== Removing Check-In Columns from Inscriptions Table ===\n\n";

    // Remove columns
    $sql1 = "ALTER TABLE inscriptions DROP COLUMN IF EXISTS checked_in";
    $pdo->exec($sql1);
    echo "✓ Removed 'checked_in' column\n";

    $sql2 = "ALTER TABLE inscriptions DROP COLUMN IF EXISTS check_in_time";
    $pdo->exec($sql2);
    echo "✓ Removed 'check_in_time' column\n";

    $sql3 = "ALTER TABLE inscriptions DROP COLUMN IF EXISTS check_in_by";
    $pdo->exec($sql3);
    echo "✓ Removed 'check_in_by' column\n";

    echo "\n✅ Rollback completed successfully!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
