<?php
/**
 * CONTRÔLEUR DE RÉINITIALISATION DE MOT DE PASSE
 * ================================================
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/LoginRateLimiter.php';
require_once __DIR__ . '/../services/MailService.php';

class ForgotPasswordController
{
    private $conn;
    private $userModel;
    private $rateLimiter;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->userModel = new User();

        // Rate limiter pour éviter l'abus (3 tentatives max par 15 min)
        $this->rateLimiter = new LoginRateLimiter($this->conn, 3, 900, 900);
    }

    /**
     * Afficher la page "Mot de passe oublié"
     */
    public function forgotPasswordPage()
    {
        include __DIR__ . '/../views/auth/forgot_password.php';
    }

    /**
     * Traiter la demande de réinitialisation
     */
    public function processForgotPassword()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php?page=forgot_password");
            exit();
        }

        $email = trim($_POST["email"] ?? '');

        // Validation basique
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["forgot_error"] = "Veuillez entrer une adresse email valide.";
            header("Location: index.php?page=forgot_password");
            exit();
        }

        // Vérifier le rate limiting (anti-spam)
        if ($this->rateLimiter->isLocked($email)) {
            $timeRemaining = $this->rateLimiter->getTimeUntilUnlock($email);
            $minutes = ceil($timeRemaining / 60);

            $_SESSION["forgot_error"] = "Trop de tentatives. Veuillez réessayer dans {$minutes} minute(s).";
            header("Location: index.php?page=forgot_password");
            exit();
        }

        // Enregistrer la tentative
        $this->rateLimiter->recordAttempt($email, false);

        // Vérifier si l'utilisateur existe
        $user = $this->userModel->getUserByEmail($email);

        // IMPORTANT: Toujours afficher le même message (anti-énumération)
        if ($user) {
            // Générer un token sécurisé
            $token = $this->generateSecureToken();

            // Supprimer les anciens tokens pour cet email
            $this->deleteOldTokens($email);

            // Insérer le nouveau token
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $this->insertResetToken($email, $token, $expiresAt);

            // Envoyer l'email avec le lien de réinitialisation
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $domain = $_SERVER['HTTP_HOST'];
            $path = dirname($_SERVER['SCRIPT_NAME']);

            // Nettoyer le path pour éviter les doubles slashs ou les points
            if ($path === '/' || $path === '\\')
                $path = '';

            $resetLink = "$protocol://$domain$path/?page=reset_password&token=" . $token;

            $mailService = new MailService();
            $mailService->sendPasswordResetEmail($email, $resetLink);
        } else {
            // Fake delay to simulate email sending time
            usleep(random_int(200000, 500000));
        }

        // Toujours afficher le même message de succès
        $_SESSION["forgot_success"] = "Si cette adresse email existe, vous recevrez un lien de réinitialisation dans quelques instants.";
        header("Location: index.php?page=forgot_password");
        exit();
    }

    /**
     * Afficher la page de réinitialisation avec token
     */
    public function resetPasswordPage()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $_SESSION["reset_error"] = "Token invalide.";
            header("Location: index.php?page=login");
            exit();
        }

        // Vérifier si le token est valide et non expiré
        $resetData = $this->getResetData($token);

        if (!$resetData) {
            $_SESSION["reset_error"] = "Ce lien de réinitialisation est invalide ou a expiré.";
            header("Location: index.php?page=forgot_password");
            exit();
        }

        // Passer les données à la vue
        include __DIR__ . '/../views/auth/reset_password.php';
    }

    /**
     * Traiter le nouveau mot de passe
     */
    public function processResetPassword()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php?page=login");
            exit();
        }

        $token = trim($_POST["token"] ?? '');
        $newPassword = trim($_POST["password"] ?? '');
        $confirmPassword = trim($_POST["confirm_password"] ?? '');

        // Validations
        if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION["reset_error"] = "Tous les champs sont requis.";
            header("Location: index.php?page=reset_password&token=" . urlencode($token));
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION["reset_error"] = "Les mots de passe ne correspondent pas.";
            header("Location: index.php?page=reset_password&token=" . urlencode($token));
            exit();
        }

        if (strlen($newPassword) < 8) {
            $_SESSION["reset_error"] = "Le mot de passe doit contenir au moins 8 caractères.";
            header("Location: index.php?page=reset_password&token=" . urlencode($token));
            exit();
        }

        // Vérifier le token
        $resetData = $this->getResetData($token);

        if (!$resetData) {
            $_SESSION["reset_error"] = "Ce lien de réinitialisation est invalide ou a expiré.";
            header("Location: index.php?page=forgot_password");
            exit();
        }

        // Récupérer l'utilisateur
        $user = $this->userModel->getUserByEmail($resetData['email']);

        if (!$user) {
            $_SESSION["reset_error"] = "Utilisateur introuvable.";
            header("Location: index.php?page=login");
            exit();
        }

        // Mettre à jour le mot de passe
        if ($this->userModel->updatePassword($user['id'], $newPassword)) {
            // Supprimer le token utilisé
            $this->deleteToken($token);

            // Supprimer toutes les tentatives de connexion pour cet email
            $this->rateLimiter->resetAttempts($resetData['email']);

            $_SESSION["login_success"] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            header("Location: index.php?page=login");
            exit();
        }

        $_SESSION["reset_error"] = "Une erreur est survenue lors de la réinitialisation.";
        header("Location: index.php?page=reset_password&token=" . urlencode($token));
        exit();
    }

    /**
     * Générer un token cryptographiquement sécurisé
     */
    private function generateSecureToken()
    {
        // 32 bytes = 64 caractères en hexadécimal
        return bin2hex(random_bytes(32));
    }

    /**
     * Insérer un token de réinitialisation
     */
    private function insertResetToken($email, $token, $expiresAt)
    {
        $query = "INSERT INTO password_resets (email, token, expires_at) 
                  VALUES (:email, :token, :expires_at)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expiresAt);
        return $stmt->execute();
    }

    /**
     * Supprimer les anciens tokens pour un email
     */
    private function deleteOldTokens($email)
    {
        $query = "DELETE FROM password_resets WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    /**
     * Supprimer un token spécifique
     */
    private function deleteToken($token)
    {
        $query = "DELETE FROM password_resets WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }

    /**
     * Récupérer les données d'un token valide
     */
    private function getResetData($token)
    {
        $query = "SELECT email, expires_at 
                  FROM password_resets 
                  WHERE token = :token 
                  AND expires_at > NOW() 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
