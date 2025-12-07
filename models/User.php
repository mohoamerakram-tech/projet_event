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

    // Vérifier le login avec password_verify()
    public function login($email, $password)
    {
        $user = $this->getUserByEmail($email);

        if ($user && password_verify($password, $user["mot_de_passe"])) {
            // Vérifier si le hash doit être mis à jour (rehash)
            if (password_needs_rehash($user["mot_de_passe"], PASSWORD_BCRYPT, ['cost' => 12])) {
                $this->updatePassword($user['id'], $password);
            }
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
        // Hasher le mot de passe avec bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $role = htmlspecialchars(strip_tags($role));
        // Avatar is nullable, don't strip tags if null, or handle appropriately
        if ($avatar) {
            $avatar = htmlspecialchars(strip_tags($avatar));
        }

        // Liaison des paramètres
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":avatar", $avatar);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    // Mettre à jour le mot de passe
    public function updatePassword($id, $newPassword)
    {
        $query = "UPDATE " . $this->table . " SET mot_de_passe = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":id", $id);

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

    // Compter le nombre total d'utilisateurs
    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Récupérer tous les utilisateurs
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
