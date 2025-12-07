<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    if ($pdo) {
        // Check if column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM evenements LIKE 'capacite'");
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE evenements ADD COLUMN capacite INT DEFAULT NULL");
            echo "<div style='color: green; font-family: sans-serif; padding: 20px; border: 1px solid green; background: #e6fffa; border-radius: 8px;'>
                    <h3>✅ Success!</h3>
                    <p>Column 'capacite' added to 'evenements' table successfully.</p>
                    <p>You can now go back to <a href='public/index.php'>Home</a></p>
                  </div>";
        } else {
            echo "<div style='color: blue; font-family: sans-serif; padding: 20px; border: 1px solid blue; background: #ebf8ff; border-radius: 8px;'>
                    <h3>ℹ️ Info</h3>
                    <p>Column 'capacite' already exists.</p>
                     <p>You can go back to <a href='public/index.php'>Home</a></p>
                  </div>";
        }
    } else {
        echo "Database connection failed.";
    }
} catch (Exception $e) {
    echo "<div style='color: red; font-family: sans-serif; padding: 20px; border: 1px solid red; background: #fff5f5; border-radius: 8px;'>
            <h3>❌ Error</h3>
            <p>" . $e->getMessage() . "</p>
          </div>";
}
