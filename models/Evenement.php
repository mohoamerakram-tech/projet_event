<?php
// models/Evenement.php

class Evenement
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAll($onlyUpcoming = false, $query = null)
    {
        $sql = "
            SELECT e.*, c.nom as category_name, COUNT(i.id) as participant_count
            FROM evenements e
            LEFT JOIN categories c ON e.category_id = c.id
            LEFT JOIN inscriptions i ON e.id = i.evenement_id
        ";

        $params = [];
        $whereCli = [];

        if ($onlyUpcoming) {
            $whereCli[] = "(e.date_event > CURDATE() OR (e.date_event = CURDATE() AND e.heure >= CURTIME()))";
        }

        if (!empty($query)) {
            $whereCli[] = "(e.titre LIKE ? OR e.description LIKE ? OR e.lieu LIKE ?)";
            $term = "%$query%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($whereCli)) {
            $sql .= " WHERE " . implode(' AND ', $whereCli);
        }

        $sql .= " GROUP BY e.id ";

        if ($onlyUpcoming) {
            $sql .= " HAVING (e.capacite IS NULL OR e.capacite = 0 OR participant_count < e.capacite) ";
            $sql .= " ORDER BY e.date_event ASC, e.heure ASC";
        } else {
            // Admin view: Newest added first
            $sql .= " ORDER BY e.id DESC";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($query = null, $categoryId = null, $onlyUpcoming = false)
    {
        $sql = "SELECT e.*, c.nom as category_name, COUNT(i.id) as participant_count 
                FROM evenements e
                LEFT JOIN categories c ON e.category_id = c.id
                LEFT JOIN inscriptions i ON e.id = i.evenement_id
                WHERE 1=1";

        $params = [];

        if (!empty($query)) {
            $sql .= " AND (e.titre LIKE ? OR e.description LIKE ? OR e.lieu LIKE ?)";
            $term = "%$query%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($categoryId)) {
            $sql .= " AND e.category_id = ?";
            $params[] = $categoryId;
        }

        if ($onlyUpcoming) {
            $sql .= " AND (e.date_event > CURDATE() OR (e.date_event = CURDATE() AND e.heure >= CURTIME()))";
        }

        $sql .= " GROUP BY e.id ";

        if ($onlyUpcoming) {
            $sql .= " HAVING (e.capacite IS NULL OR e.capacite = 0 OR participant_count < e.capacite) ";
        }

        $sql .= " ORDER BY e.date_event ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM evenements WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($titre, $description, $date_event, $lieu, $heure, $image, $category_id, $capacite = null)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO evenements (titre, description, date_event, lieu, heure, image, category_id, capacite)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([$titre, $description, $date_event, $lieu, $heure, $image, $category_id, $capacite]);
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }



    public function update($id, $titre, $description, $date_event, $lieu, $heure, $image, $category_id, $capacite = null)
    {
        $stmt = $this->pdo->prepare("
            UPDATE evenements 
            SET titre=?, description=?, date_event=?, lieu=?, heure=?, image=?, category_id=?, capacite=? 
            WHERE id=?
        ");

        $stmt->execute([$titre, $description, $date_event, $lieu, $heure, $image, $category_id, $capacite, $id]);
    }


    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM evenements WHERE id=?");
        $stmt->execute([$id]);
    }
}
