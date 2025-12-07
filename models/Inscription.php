<?php
class Inscription
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($evenement_id, $nom, $email)
    {
        // Check if already registered
        $stmt = $this->pdo->prepare("SELECT id FROM inscriptions WHERE evenement_id = ? AND email_participant = ?");
        $stmt->execute([$evenement_id, $email]);
        if ($stmt->fetch()) {
            return false; // Already registered
        }

        $stmt = $this->pdo->prepare("INSERT INTO inscriptions (evenement_id, nom_participant, email_participant) VALUES (?, ?, ?)");
        return $stmt->execute([$evenement_id, $nom, $email]);
    }

    public function getByUserId($email)
    {
        $stmt = $this->pdo->prepare("
            SELECT i.*, e.titre, e.date_event, e.lieu, e.image, e.heure, e.description 
            FROM inscriptions i
            JOIN evenements e ON i.evenement_id = e.id
            WHERE i.email_participant = ?
            ORDER BY e.date_event DESC
        ");
        $stmt->execute([$email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancel($inscription_id, $email)
    {
        $stmt = $this->pdo->prepare("DELETE FROM inscriptions WHERE id = ? AND email_participant = ?");
        return $stmt->execute([$inscription_id, $email]);
    }
    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT i.*, e.titre as event_title, e.date_event 
            FROM inscriptions i
            JOIN evenements e ON i.evenement_id = e.id
            ORDER BY i.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventStats($search = null)
    {
        $sql = "
            SELECT e.id, e.titre, e.date_event, e.lieu, e.image, COUNT(i.id) as total_participants
            FROM evenements e
            LEFT JOIN inscriptions i ON e.id = i.evenement_id
        ";

        $params = [];
        if ($search) {
            $sql .= " WHERE e.titre LIKE ? OR e.lieu LIKE ? ";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " GROUP BY e.id ORDER BY e.date_event DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByEventId($eventId, $search = null)
    {
        $sql = "
            SELECT i.*, e.titre as event_title, e.date_event, u.avatar as user_avatar, u.nom as user_name, u.id as user_id 
            FROM inscriptions i
            JOIN evenements e ON i.evenement_id = e.id
            LEFT JOIN users u ON i.email_participant = u.email
            WHERE i.evenement_id = ?
        ";

        $params = [$eventId];

        if ($search) {
            $sql .= " AND (i.nom_participant LIKE ? OR i.email_participant LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY i.nom_participant ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
