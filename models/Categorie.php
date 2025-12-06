<?php
class Categorie
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByName($nom)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE nom = ?");
        $stmt->execute([$nom]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nom)
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
        $stmt->execute([$nom]);
        return $this->pdo->lastInsertId();
    }
}
