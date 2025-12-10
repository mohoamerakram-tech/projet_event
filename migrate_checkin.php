<?php
/**
 * Migration Script: Add Check-In Columns to Inscriptions Table
 * Run this file once to update the database schema
 */

require_once 'config/db.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    echo "=== Adding Check-In Columns to Inscriptions Table ===\n\n";

    // Add checked_in column
    $sql1 = "ALTER TABLE inscriptions ADD COLUMN IF NOT EXISTS checked_in TINYINT(1) DEFAULT 0";
    $pdo->exec($sql1);
    echo "✓ Added 'checked_in' column\n";

    // Add check_in_time column
    $sql2 = "ALTER TABLE inscriptions ADD COLUMN IF NOT EXISTS check_in_time DATETIME NULL";
    $pdo->exec($sql2);
    echo "✓ Added 'check_in_time' column\n";

    // Add check_in_by column (admin who performed check-in)
    $sql3 = "ALTER TABLE inscriptions ADD COLUMN IF NOT EXISTS check_in_by INT NULL";
    $pdo->exec($sql3);
    echo "✓ Added 'check_in_by' column\n";

    echo "\n✅ Migration completed successfully!\n";
    echo "\nYou can now use the check-in functionality.\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
