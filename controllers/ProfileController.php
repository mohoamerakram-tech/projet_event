<?php

/**
 * PROFILE CONTROLLER
 * ==================
 * Handles admin and user profile management
 */

class ProfileController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Display admin profile page
     */
    public function adminProfile()
    {
        // Check if user is admin
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
            header("Location: ?page=login");
            exit();
        }

        // Get admin user details
        $userId = $_SESSION["user"]["id"];

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                header("Location: ?page=admin_dashboard");
                exit();
            }

            // Get admin statistics
            $stats = $this->getAdminStats($userId);

            // Get recent activity
            $recentActivity = $this->getRecentActivity($userId);

            include __DIR__ . '/../views/admin/profile.php';
        } catch (Exception $e) {
            error_log("Profile error: " . $e->getMessage());
            header("Location: ?page=admin_dashboard");
            exit();
        }
    }

    /**
     * Get admin statistics
     */
    private function getAdminStats($userId)
    {
        $stats = [];

        try {
            // Total events (all events since there's no created_by column)
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM evenements");
            $stats['total_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Total participants across all events
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM inscriptions");
            $stats['total_participants'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Total categories
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM categories");
            $stats['total_categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Upcoming events
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM evenements WHERE date_event > CURDATE()");
            $stats['upcoming_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Stats error: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get recent admin activity
     */
    private function getRecentActivity($userId)
    {
        $activity = [];

        try {
            // Get recent events (all events since there's no created_by column)
            $stmt = $this->pdo->query("
                SELECT 
                    e.id,
                    e.titre,
                    e.date_event,
                    c.nom as category,
                    COUNT(i.id) as participants
                FROM evenements e
                LEFT JOIN categories c ON e.category_id = c.id
                LEFT JOIN inscriptions i ON e.id = i.evenement_id
                GROUP BY e.id, e.titre, e.date_event, c.nom
                ORDER BY e.id DESC
                LIMIT 5
            ");
            $activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Activity error: " . $e->getMessage());
        }

        return $activity;
    }

    /**
     * Update admin profile
     */
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?page=admin_profile");
            exit();
        }

        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
            header("Location: ?page=login");
            exit();
        }

        $userId = $_SESSION["user"]["id"];
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];

        // Validation
        if (empty($nom)) {
            $errors[] = "Le nom est requis";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }

        // Check if email is already used by another user
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $errors[] = "Cet email est déjà utilisé";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur de vérification";
        }

        if (!empty($errors)) {
            $_SESSION['profile_errors'] = $errors;
            header("Location: ?page=admin_profile");
            exit();
        }

        // Update profile
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
            $stmt->execute([$nom, $email, $userId]);

            // Update session data
            $_SESSION["user"]["nom"] = $nom;
            $_SESSION["user"]["email"] = $email;

            // Notify Admin
            require_once __DIR__ . '/../models/Notification.php';
            $notificationModel = new Notification($this->pdo);
            $notificationModel->create(
                $userId,
                "Profile Updated: Your personal information has been successfully updated.",
                "success"
            );

            $_SESSION['profile_success'] = "Profil mis à jour avec succès";
            header("Location: ?page=admin_profile");
            exit();
        } catch (Exception $e) {
            error_log("Update profile error: " . $e->getMessage());
            $_SESSION['profile_errors'] = ["Erreur lors de la mise à jour"];
            header("Location: ?page=admin_profile");
            exit();
        }
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $redirectPage = ($_SESSION["user"]["role"] === "admin") ? "admin_profile" : "user_profile";
            header("Location: ?page=" . $redirectPage);
            exit();
        }

        if (!isset($_SESSION["user"])) {
            header("Location: ?page=login");
            exit();
        }

        $userId = $_SESSION["user"]["id"];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $errors[] = "Tous les champs sont requis";
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }

        if (strlen($newPassword) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
        }

        // Verify current password
        try {
            $stmt = $this->pdo->prepare("SELECT mot_de_passe FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['mot_de_passe'])) {
                $errors[] = "Mot de passe actuel incorrect";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur de vérification";
        }

        // Determine redirect page based on role
        $redirectPage = ($_SESSION["user"]["role"] === "admin") ? "admin_profile" : "user_profile";

        if (!empty($errors)) {
            $_SESSION['password_errors'] = $errors;
            header("Location: ?page=" . $redirectPage);
            exit();
        }

        // Update password
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare("UPDATE users SET mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);

            // Notify User/Admin
            require_once __DIR__ . '/../models/Notification.php';
            $notificationModel = new Notification($this->pdo);
            $notificationModel->create(
                $userId,
                "Security Alert: Your password was changed successfully.",
                "warning",
                null
            );

            $_SESSION['password_success'] = "Mot de passe modifié avec succès";
            header("Location: ?page=" . $redirectPage);
            exit();
        } catch (Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            $_SESSION['password_errors'] = ["Erreur lors de la modification"];
            header("Location: ?page=" . $redirectPage);
            exit();
        }
    }

    /**
     * Display user profile page
     */
    public function userProfile()
    {
        // Check if user is logged in
        if (!isset($_SESSION["user"])) {
            header("Location: ?page=login");
            exit();
        }

        // Get user details
        $userId = $_SESSION["user"]["id"];

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                header("Location: ?page=home");
                exit();
            }

            // Get user statistics
            $stats = $this->getUserStats($userId);

            // Get user registrations
            $registrations = $this->getUserRegistrations($userId);

            include __DIR__ . '/../views/users/profile.php';
        } catch (Exception $e) {
            error_log("User profile error: " . $e->getMessage());
            header("Location: ?page=home");
            exit();
        }
    }

    /**
     * Get user statistics
     */
    private function getUserStats($userId)
    {
        $stats = [];

        try {
            // Get user email first
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $userEmail = $user['email'] ?? null;

            if (!$userEmail) {
                return ['total_registrations' => 0, 'upcoming_events' => 0, 'past_events' => 0];
            }

            // Total events registered (using email_participant)
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM inscriptions WHERE email_participant = ?");
            $stmt->execute([$userEmail]);
            $stats['total_registrations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Upcoming events
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM inscriptions i
                JOIN evenements e ON i.evenement_id = e.id
                WHERE i.email_participant = ? AND e.date_event > CURDATE()
            ");
            $stmt->execute([$userEmail]);
            $stats['upcoming_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // Past events
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM inscriptions i
                JOIN evenements e ON i.evenement_id = e.id
                WHERE i.email_participant = ? AND e.date_event <= CURDATE()
            ");
            $stmt->execute([$userEmail]);
            $stats['past_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (Exception $e) {
            error_log("User stats error: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get user event registrations
     */
    private function getUserRegistrations($userId)
    {
        $registrations = [];

        try {
            // Get user email first
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $userEmail = $user['email'] ?? null;

            if (!$userEmail) {
                return [];
            }

            $stmt = $this->pdo->prepare("
                SELECT 
                    e.id,
                    e.titre,
                    e.date_event,
                    e.lieu,
                    c.nom as category,
                    i.date_inscription,
                    CASE WHEN e.date_event > CURDATE() THEN 'upcoming' ELSE 'past' END as status
                FROM inscriptions i
                JOIN evenements e ON i.evenement_id = e.id
                LEFT JOIN categories c ON e.category_id = c.id
                WHERE i.email_participant = ?
                ORDER BY e.date_event DESC
                LIMIT 10
            ");
            $stmt->execute([$userEmail]);
            $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("User registrations error: " . $e->getMessage());
        }

        return $registrations;
    }

    /**
     * Update user profile
     */
    public function updateUserProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?page=user_profile");
            exit();
        }

        if (!isset($_SESSION["user"])) {
            header("Location: ?page=login");
            exit();
        }

        $userId = $_SESSION["user"]["id"];
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];

        // Validation
        if (empty($nom)) {
            $errors[] = "Le nom est requis";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }

        // Check if email is already used by another user
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $errors[] = "Cet email est déjà utilisé";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur de vérification";
        }

        if (!empty($errors)) {
            $_SESSION['profile_errors'] = $errors;
            header("Location: ?page=user_profile");
            exit();
        }

        // Update profile
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
            $stmt->execute([$nom, $email, $userId]);

            // Update session data
            $_SESSION["user"]["nom"] = $nom;
            $_SESSION["user"]["email"] = $email;

            // Notify User
            require_once __DIR__ . '/../models/Notification.php';
            $notificationModel = new Notification($this->pdo);
            $notificationModel->create(
                $userId,
                "Profile Updated: Your personal information has been successfully updated.",
                "success"
            );

            $_SESSION['profile_success'] = "Profil mis à jour avec succès";
            header("Location: ?page=user_profile");
            exit();
        } catch (Exception $e) {
            error_log("Update user profile error: " . $e->getMessage());
            $_SESSION['profile_errors'] = ["Erreur lors de la mise à jour"];
            header("Location: ?page=user_profile");
            exit();
        }
    }

    /**
     * Update user avatar
     */
    public function updateAvatar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $redirectPage = ($_SESSION["user"]["role"] === "admin") ? "admin_profile" : "user_profile";
            header("Location: ?page=" . $redirectPage);
            exit();
        }

        if (!isset($_SESSION["user"])) {
            header("Location: ?page=login");
            exit();
        }

        $userId = $_SESSION["user"]["id"];
        $errors = [];

        // Determine redirect page based on role
        $redirectPage = ($_SESSION["user"]["role"] === "admin") ? "admin_profile" : "user_profile";

        // Check if file was uploaded
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['profile_errors'] = ["Aucun fichier sélectionné"];
            header("Location: ?page=" . $redirectPage);
            exit();
        }

        $file = $_FILES['avatar'];

        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['profile_errors'] = ["Erreur lors de l'upload du fichier"];
            header("Location: ?page=" . $redirectPage);
            exit();
        }

        // Check file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            $_SESSION['profile_errors'] = ["Le fichier est trop volumineux (max 2MB)"];
            header("Location: ?page=" . $redirectPage);
            exit();
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            $_SESSION['profile_errors'] = ["Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP"];
            header("Location: ?page=" . $redirectPage);
            exit();
        }

        try {
            // Get current avatar
            $stmt = $this->pdo->prepare("SELECT avatar FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $oldAvatar = $user['avatar'] ?? null;

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $uploadDir = __DIR__ . '/../public/uploads/avatars/';

            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadPath = $uploadDir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $_SESSION['profile_errors'] = ["Erreur lors de l'enregistrement du fichier"];
                header("Location: ?page=" . $redirectPage);
                exit();
            }

            // Update database
            $avatarPath = 'uploads/avatars/' . $filename;
            $stmt = $this->pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$avatarPath, $userId]);

            // Update session
            $_SESSION["user"]["avatar"] = $avatarPath;

            // Delete old avatar if exists
            if ($oldAvatar && file_exists(__DIR__ . '/../public/' . $oldAvatar)) {
                unlink(__DIR__ . '/../public/' . $oldAvatar);
            }

            $_SESSION['profile_success'] = "Photo de profil mise à jour avec succès";
            header("Location: ?page=" . $redirectPage);
            exit();

        } catch (Exception $e) {
            error_log("Avatar upload error: " . $e->getMessage());
            $_SESSION['profile_errors'] = ["Erreur lors de la mise à jour de la photo"];
            header("Location: ?page=" . $redirectPage);
            exit();
        }
    }
}
