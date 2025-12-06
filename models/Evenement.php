<?php
// models/Evenement.php

class Evenement
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT e.*, c.nom as category_name 
            FROM evenements e
            LEFT JOIN categories c ON e.category_id = c.id
            ORDER BY e.date_event ASC, e.heure ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($query = null, $categoryId = null)
    {
        $sql = "SELECT e.*, c.nom as category_name 
                FROM evenements e
                LEFT JOIN categories c ON e.category_id = c.id
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

    public function create($titre, $description, $date_event, $lieu, $heure, $image, $category_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO evenements (titre, description, date_event, lieu, heure, image, category_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([$titre, $description, $date_event, $lieu, $heure, $image, $category_id]);
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }



    public function update($id, $titre, $description, $date_event, $lieu, $heure, $image, $category_id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE evenements 
            SET titre=?, description=?, date_event=?, lieu=?, heure=?, image=?, category_id=? 
            WHERE id=?
        ");

        $stmt->execute([$titre, $description, $date_event, $lieu, $heure, $image, $category_id, $id]);
    }


    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM evenements WHERE id=?");
        $stmt->execute([$id]);
    }
}
