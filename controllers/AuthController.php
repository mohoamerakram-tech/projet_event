<?php
//session_start();

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/LoginRateLimiter.php';

class AuthController
{

    private $userModel;
    private $rateLimiter;

    public function __construct()
    {
        $this->userModel = new User();

        // Initialiser le rate limiter (5 tentatives max, verrouillage 15 min)
        $database = new Database();
        $this->rateLimiter = new LoginRateLimiter($database->getConnection(), 5, 900, 300);
    }

    // Afficher la page de login
    public function loginPage()
    {
        include __DIR__ . '/../views/auth/login.php';
    }

    // Traitement du login
    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);

            // Vérifier si l'IP/email est verrouillé
            if ($this->rateLimiter->isLocked($email)) {
                $timeRemaining = $this->rateLimiter->getTimeUntilUnlock($email);
                $minutes = ceil($timeRemaining / 60);

                $_SESSION["login_error"] = "Trop de tentatives échouées. Veuillez réessayer dans {$minutes} minute(s).";
                header("Location: index.php?page=login");
                exit();
            }

            // Vérifier les informations
            $user = $this->userModel->login($email, $password);

            if ($user) {
                // Connexion réussie
                $this->rateLimiter->recordAttempt($email, true);
                $this->rateLimiter->resetAttempts($email);

                // Créer la session
                $_SESSION["user"] = $user;

                // Redirection selon rôle
                if ($user["role"] === "admin") {
                    header("Location: index.php?page=admin_dashboard");
                } else {
                    header("Location: index.php?page=user_events");
                }
                exit();
            } else {
                // Échec de connexion
                $this->rateLimiter->recordAttempt($email, false);

                $remainingAttempts = $this->rateLimiter->getRemainingAttempts($email);

                if ($remainingAttempts > 0) {
                    $_SESSION["login_error"] = "Email ou mot de passe incorrect. Il vous reste {$remainingAttempts} tentative(s).";
                } else {
                    $_SESSION["login_error"] = "Compte temporairement verrouillé suite à trop de tentatives échouées.";
                }

                header("Location: index.php?page=login");
                exit();
            }
        }
    }

    // Déconnexion
    public function logout()
    {
        session_destroy();
        header("Location: ../public/?page=login");
        exit();
    }

    // Afficher la page d'inscription
    public function registerPage()
    {
        include __DIR__ . '/../views/auth/register.php';
    }

    // Traitement de l'inscription
    public function register()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nom = trim($_POST["nom"]);
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);
            $avatarPath = null;

            // Gestion de l'avatar
            if (isset($_FILES['avatar']) && $_FILES['avatar']['name'] !== '') {
                // Check for upload errors
                if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                    $uploadErrors = [
                        UPLOAD_ERR_INI_SIZE => "L'image est trop volumineuse (max " . ini_get('upload_max_filesize') . ").",
                        UPLOAD_ERR_FORM_SIZE => "L'image dépasse la taille limite du formulaire.",
                        UPLOAD_ERR_PARTIAL => "L'image n'a été que partiellement téléchargée.",
                        UPLOAD_ERR_NO_FILE => "Aucun fichier n'a été téléchargé.",
                        UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant.",
                        UPLOAD_ERR_CANT_WRITE => "Échec de l'écriture du fichier sur le disque.",
                        UPLOAD_ERR_EXTENSION => "Une extension PHP a arrêté l'envoi de fichier."
                    ];
                    $errorCode = $_FILES['avatar']['error'];
                    $_SESSION["register_error"] = isset($uploadErrors[$errorCode]) ? $uploadErrors[$errorCode] : "Erreur inconnue lors du téléchargement.";
                    header("Location: index.php?page=register");
                    exit();
                }

                $uploadDir = __DIR__ . '/../public/uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                $fileSize = $_FILES['avatar']['size'];
                $fileType = $_FILES['avatar']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp', 'jpeg');
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $dest_path = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $avatarPath = 'uploads/avatars/' . $newFileName;
                    } else {
                        $_SESSION["register_error"] = "Erreur lors de la sauvegarde de l'image.";
                        header("Location: index.php?page=register");
                        exit();
                    }
                } else {
                    $_SESSION["register_error"] = "Format d'image non supporté. Utilisez JPG, PNG, GIF ou WEBP.";
                    header("Location: index.php?page=register");
                    exit();
                }
            }

            // Vérifier si l'email existe déjà
            if ($this->userModel->getUserByEmail($email)) {
                $_SESSION["register_error"] = "Cet email est déjà utilisé.";
                header("Location: index.php?page=register");
                exit();
            }

            // Créer l'utilisateur
            if ($this->userModel->create($nom, $email, $password, 'user', $avatarPath)) {
                // Succès -> redirection vers la page de succès
                header("Location: index.php?page=register_success");
                exit();
            } else {
                $_SESSION["register_error"] = "Une erreur est survenue lors de l'inscription.";
                header("Location: index.php?page=register");
                exit();
            }
        }
    }

    // Page de succès d'inscription
    public function registerSuccessPage()
    {
        include __DIR__ . '/../views/auth/register_success.php';
    }
}
