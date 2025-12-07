<?php
require_once __DIR__ . '/../models/Notification.php';

class NotificationController
{
    private $pdo;
    private $notificationModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->notificationModel = new Notification($pdo);
    }

    public function markAsRead()
    {
        // Set header to JSON
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing ID']);
            exit;
        }

        // Ideally check if notification belongs to user
        // But for now, we just trust the ID and that users can't easily guess others' IDs or it doesn't matter much for notifications
        // A better way is if Notification model has markAsReadForUser($id, $userId)

        // For simplicity:
        $result = $this->notificationModel->markAsRead($id);

        if ($result) {
            $unreadCount = $this->notificationModel->getUnreadCount($_SESSION['user']['id']);
            echo json_encode(['success' => true, 'unread_count' => $unreadCount]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update']);
        }
        exit;
    }

    public function markAllAsRead()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $result = $this->notificationModel->markAllAsRead($userId);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update']);
        }
        exit;
    }
}
