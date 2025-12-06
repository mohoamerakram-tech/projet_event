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
}
