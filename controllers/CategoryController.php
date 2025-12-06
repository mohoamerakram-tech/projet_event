<?php
require_once __DIR__ . '/../models/Categorie.php';

class CategoryController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addAjax()
    {
        // Security check: Only admins should create categories
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        $nom = trim($input['nom'] ?? '');

        if (empty($nom)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Category name is required']);
            exit;
        }

        $categorieModel = new Categorie($this->pdo);

        // Check duplicate
        if ($categorieModel->findByName($nom)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Category already exists']);
            exit;
        }

        // Create
        try {
            $id = $categorieModel->create($nom);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'id' => $id, 'nom' => $nom]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        exit;
    }
}
