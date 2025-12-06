<?php
//session_start();

require_once __DIR__ . '/../models/User.php';

class AuthController
{

    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
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

            // Vérifier les informations
            $user = $this->userModel->login($email, $password);

            if ($user) {
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
                // Erreur → renvoyer vers login
                $_SESSION["login_error"] = "Email ou mot de passe incorrect.";
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
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
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
                    }
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
