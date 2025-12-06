<?php
/**
 * SYSTÈME ANTI-BRUTE FORCE
 * =========================
 * Protection contre les attaques par force brute sur le login
 * 
 * Fonctionnalités:
 * - Rate limiting par IP
 * - Verrouillage temporaire après X tentatives échouées
 * - Nettoyage automatique des anciennes tentatives
 */

class LoginRateLimiter
{
    private $conn;
    private $maxAttempts;
    private $lockoutDuration; // en secondes
    private $attemptWindow; // en secondes

    /**
     * @param PDO $connection Connexion à la base de données
     * @param int $maxAttempts Nombre max de tentatives (défaut: 5)
     * @param int $lockoutDuration Durée du verrouillage en secondes (défaut: 900 = 15 min)
     * @param int $attemptWindow Fenêtre de temps pour compter les tentatives (défaut: 300 = 5 min)
     */
    public function __construct($connection, $maxAttempts = 5, $lockoutDuration = 900, $attemptWindow = 300)
    {
        $this->conn = $connection;
        $this->maxAttempts = $maxAttempts;
        $this->lockoutDuration = $lockoutDuration;
        $this->attemptWindow = $attemptWindow;

        // Créer la table si elle n'existe pas
        $this->createTableIfNotExists();
    }

    /**
     * Créer la table de suivi des tentatives de connexion
     */
    private function createTableIfNotExists()
    {
        $query = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(255),
            attempt_time DATETIME NOT NULL,
            success TINYINT(1) DEFAULT 0,
            INDEX idx_ip_time (ip_address, attempt_time),
            INDEX idx_email_time (email, attempt_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->conn->exec($query);
    }

    /**
     * Enregistrer une tentative de connexion
     */
    public function recordAttempt($email, $success = false)
    {
        $ipAddress = $this->getClientIp();

        $query = "INSERT INTO login_attempts (ip_address, email, attempt_time, success) 
                  VALUES (:ip, :email, NOW(), :success)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ip', $ipAddress);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':success', $success, PDO::PARAM_INT);
        $stmt->execute();

        // Nettoyer les anciennes tentatives (plus de 24h)
        $this->cleanOldAttempts();
    }

    /**
     * Vérifier si l'IP est verrouillée
     */
    public function isLocked($email = null)
    {
        $ipAddress = $this->getClientIp();

        // Compter les tentatives échouées dans la fenêtre de temps
        $query = "SELECT COUNT(*) as attempt_count 
                  FROM login_attempts 
                  WHERE ip_address = :ip 
                  AND success = 0 
                  AND attempt_time > DATE_SUB(NOW(), INTERVAL :window SECOND)";

        if ($email) {
            $query .= " AND email = :email";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ip', $ipAddress);
        $stmt->bindParam(':window', $this->attemptWindow, PDO::PARAM_INT);

        if ($email) {
            $stmt->bindParam(':email', $email);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['attempt_count'] >= $this->maxAttempts;
    }

    /**
     * Obtenir le temps restant avant déblocage
     */
    public function getTimeUntilUnlock($email = null)
    {
        $ipAddress = $this->getClientIp();

        $query = "SELECT MAX(attempt_time) as last_attempt 
                  FROM login_attempts 
                  WHERE ip_address = :ip 
                  AND success = 0 
                  AND attempt_time > DATE_SUB(NOW(), INTERVAL :window SECOND)";

        if ($email) {
            $query .= " AND email = :email";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ip', $ipAddress);
        $stmt->bindParam(':window', $this->attemptWindow, PDO::PARAM_INT);

        if ($email) {
            $stmt->bindParam(':email', $email);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['last_attempt']) {
            $lastAttempt = strtotime($result['last_attempt']);
            $unlockTime = $lastAttempt + $this->lockoutDuration;
            $remaining = $unlockTime - time();
            return max(0, $remaining);
        }

        return 0;
    }

    /**
     * Réinitialiser les tentatives après connexion réussie
     */
    public function resetAttempts($email)
    {
        $ipAddress = $this->getClientIp();

        $query = "DELETE FROM login_attempts 
                  WHERE ip_address = :ip 
                  AND email = :email 
                  AND success = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ip', $ipAddress);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    }

    /**
     * Nettoyer les tentatives de plus de 24h
     */
    private function cleanOldAttempts()
    {
        $query = "DELETE FROM login_attempts 
                  WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $this->conn->exec($query);
    }

    /**
     * Obtenir l'IP du client (compatible avec proxies)
     */
    private function getClientIp()
    {
        $ipAddress = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        // Valider l'IP
        if (filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            return $ipAddress;
        }

        return '0.0.0.0';
    }

    /**
     * Obtenir le nombre de tentatives restantes
     */
    public function getRemainingAttempts($email = null)
    {
        $ipAddress = $this->getClientIp();

        $query = "SELECT COUNT(*) as attempt_count 
                  FROM login_attempts 
                  WHERE ip_address = :ip 
                  AND success = 0 
                  AND attempt_time > DATE_SUB(NOW(), INTERVAL :window SECOND)";

        if ($email) {
            $query .= " AND email = :email";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ip', $ipAddress);
        $stmt->bindParam(':window', $this->attemptWindow, PDO::PARAM_INT);

        if ($email) {
            $stmt->bindParam(':email', $email);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return max(0, $this->maxAttempts - $result['attempt_count']);
    }
}
