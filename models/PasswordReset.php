<?php
class PasswordReset
{
    private $conn;
    private $table = "password_resets";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Créer un token de réinitialisation
    public function createResetToken($email)
    {
        // Générer un token sécurisé
        $token = bin2hex(random_bytes(32)); // 64 caractères hexadécimaux

        // Invalider les anciens tokens pour cet email
        $this->invalidateOldTokens($email);

        // Insérer le nouveau token avec expiration dans 30 minutes
        $query = "INSERT INTO " . $this->table . " (email, token, expires_at) VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":token", $token);

        if ($stmt->execute()) {
            return $token;
        }

        return false;
    }

    // Vérifier si un token est valide
    public function verifyToken($token)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE token = :token AND expires_at > NOW() AND used = 0 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // Protection contre les timing attacks (simuler un temps de réponse variable)
            usleep(random_int(10000, 50000));
            return false;
        }

        return $result;
    }

    // Marquer un token comme utilisé
    public function markAsUsed($token)
    {
        $query = "UPDATE " . $this->table . " SET used = 1 WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        return $stmt->execute();
    }

    // Invalider les anciens tokens d'un utilisateur
    private function invalidateOldTokens($email)
    {
        $query = "DELETE FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        return $stmt->execute();
    }

    // Nettoyer les tokens expirés (à appeler via cronjob idéalement)
    public function cleanExpiredTokens()
    {
        $query = "DELETE FROM " . $this->table . " WHERE expires_at < NOW()";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
