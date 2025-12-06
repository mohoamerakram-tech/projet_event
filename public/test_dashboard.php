<?php
// Quick test script for dashboard API endpoints
session_start();

// Simulate admin login
$_SESSION["user"] = [
    "id" => 1,
    "role" => "admin",
    "nom" => "Test Admin"
];

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/DashboardController.php';

$dashboard = new DashboardController($pdo);

echo "Testing Dashboard API Endpoints\n";
echo "================================\n\n";

// Test 1: Stats
echo "1. Testing getStats():\n";
ob_start();
$dashboard->getStats();
$output = ob_get_clean();
echo $output . "\n\n";

// Test 2: Events Per Month
echo "2. Testing getEventsPerMonth():\n";
ob_start();
$dashboard->getEventsPerMonth();
$output = ob_get_clean();
echo $output . "\n\n";

// Test 3: Categories
echo "3. Testing getCategoryDistribution():\n";
ob_start();
$dashboard->getCategoryDistribution();
$output = ob_get_clean();
echo $output . "\n\n";

// Test 4: Latest Events
echo "4. Testing getLatestEvents():\n";
ob_start();
$dashboard->getLatestEvents();
$output = ob_get_clean();
echo $output . "\n\n";

echo "All tests completed!\n";
