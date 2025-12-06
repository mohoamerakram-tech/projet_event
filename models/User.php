<?php
require_once __DIR__ . '/../config/db.php';

class User
{

    private $conn;
    private $table = "users";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupérer un utilisateur par email
    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Vérifier le login
    public function login($email, $password)
    {
        $user = $this->getUserByEmail($email);

        if ($user && $user["mot_de_passe"] === $password) {
            return $user;   // Login correct
        }

        return false;       // Échec
    }


    // Vérifier si c'est un admin
    public function isAdmin($user)
    {
        return isset($user["role"]) && $user["role"] === "admin";
    }

    // Créer un nouvel utilisateur
    public function create($nom, $email, $password, $role = 'user', $avatar = null)
    {
        $query = "INSERT INTO " . $this->table . " (nom, email, mot_de_passe, role, avatar) VALUES (:nom, :email, :password, :role, :avatar)";
        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $nom = htmlspecialchars(strip_tags($nom));
        $email = htmlspecialchars(strip_tags($email));
        $password = htmlspecialchars(strip_tags($password));
        $role = htmlspecialchars(strip_tags($role));
        // Avatar is nullable, don't strip tags if null, or handle appropriately
        if ($avatar) {
            $avatar = htmlspecialchars(strip_tags($avatar));
        }

        // Liaison des paramètres
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":avatar", $avatar);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Mettre à jour l'avatar
    public function updateAvatar($id, $avatarPath)
    {
        $query = "UPDATE " . $this->table . " SET avatar = :avatar WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $avatarPath = htmlspecialchars(strip_tags($avatarPath));

        $stmt->bindParam(":avatar", $avatarPath);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
