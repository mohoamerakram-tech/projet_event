<?php
require_once __DIR__ . '/../models/Favorite.php';

// Ensure JSON response
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$favoriteModel = new Favorite();
$userId = $_SESSION['user_id'];

if ($action === 'toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $eventId = $data['event_id'] ?? null;

    if (!$eventId) {
        echo json_encode(['success' => false, 'message' => 'Event ID required']);
        exit;
    }

    if ($favoriteModel->isFavorite($userId, $eventId)) {
        $success = $favoriteModel->remove($userId, $eventId);
        $isFavorite = false;
    } else {
        $success = $favoriteModel->add($userId, $eventId);
        $isFavorite = true;
    }

    echo json_encode(['success' => $success, 'is_favorite' => $isFavorite]);
    exit;
} elseif ($action === 'list') {
    $favorites = $favoriteModel->getUserFavorites($userId);
    echo json_encode(['success' => true, 'favorites' => $favorites]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}
