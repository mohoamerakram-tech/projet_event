<?php
require_once __DIR__ . '/../config/db.php';

class Notification
{
    private $conn;
    private $table = "notifications";

    public function __construct($pdo = null)
    {
        if ($pdo) {
            $this->conn = $pdo;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }

    // Check if notification exists
    public function exists($userId, $eventId, $type)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE user_id = :user_id AND event_id = :event_id AND type = :type";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":event_id", $eventId);
        $stmt->bindParam(":type", $type);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Create a new notification
    public function create($userId, $message, $type = 'info', $eventId = null)
    {
        $query = "INSERT INTO " . $this->table . " (user_id, event_id, message, type) VALUES (:user_id, :event_id, :message, :type)";
        $stmt = $this->conn->prepare($query);

        $message = htmlspecialchars(strip_tags($message));
        $type = htmlspecialchars(strip_tags($type));

        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":event_id", $eventId);
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":type", $type);

        return $stmt->execute();
    }

    // Get all notifications for a user
    public function getAllByUserId($userId, $limit = 50)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get unread count
    public function getUnreadCount($userId)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Mark as read
    public function markAsRead($id)
    {
        $query = "UPDATE " . $this->table . " SET is_read = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Mark all as read for a user
    public function markAllAsRead($userId)
    {
        $query = "UPDATE " . $this->table . " SET is_read = 1 WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        return $stmt->execute();
    }
}
