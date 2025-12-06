<?php
// Simple database connection test
require_once __DIR__ . '/../config/db.php';

echo "Testing Database Connection\n";
echo "===========================\n\n";

try {
    // Test 1: Connection
    echo "1. Database connection: ";
    if ($pdo) {
        echo "✓ Connected\n\n";
    } else {
        echo "✗ Failed\n\n";
        exit(1);
    }

    // Test 2: Count events
    echo "2. Counting events:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evenements");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Total events: " . $result['total'] . "\n\n";

    // Test 3: Count inscriptions
    echo "3. Counting inscriptions:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM inscriptions");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Total inscriptions: " . $result['total'] . "\n\n";

    // Test 4: Count categories
    echo "4. Counting categories:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Total categories: " . $result['total'] . "\n\n";

    // Test 5: Get latest events
    echo "5. Getting latest events:\n";
    $stmt = $pdo->query("
        SELECT 
            e.id,
            e.titre,
            e.date_event,
            COUNT(i.id) as participants
        FROM evenements e
        LEFT JOIN inscriptions i ON e.id = i.id_evenement
        GROUP BY e.id, e.titre, e.date_event
        ORDER BY e.id DESC
        LIMIT 3
    ");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($events as $event) {
        echo "   - " . $event['titre'] . " (" . $event['participants'] . " participants)\n";
    }

    echo "\n✓ All database tests passed!\n";

} catch (PDOException $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
}
