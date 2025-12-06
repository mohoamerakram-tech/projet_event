<?php
require_once __DIR__ . '/../config/db.php';

class Favorite
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function add($userId, $eventId)
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO favorites (user_id, event_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $eventId]);
    }

    public function remove($userId, $eventId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND event_id = ?");
        return $stmt->execute([$userId, $eventId]);
    }

    public function isFavorite($userId, $eventId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$userId, $eventId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getUserFavorites($userId)
    {
        $stmt = $this->pdo->prepare("SELECT event_id FROM favorites WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Returns array of event_ids
    }
}
