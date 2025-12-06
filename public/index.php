<?php
session_start();

// Charger les contrôleurs
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/InscriptionController.php';

// Créer les instances
require_once __DIR__ . '/../config/db.php'; // contient $pdo

$auth = new AuthController();
$event = new EventController($pdo);        // ✅ passer $pdo
$category = new CategoryController($pdo); // si besoin
$inscription = new InscriptionController($pdo); // si besoin


// Récupérer la page demandée
$page = isset($_GET["page"]) ? $_GET["page"] : "home";

// Fonction : vérifier si l'utilisateur est connecté
function require_login()
{
    if (!isset($_SESSION["user"])) {
        header("Location: ?page=login");
        exit();
    }
}

function load_header()
{
    global $pdo; // Make $pdo available to included files
    if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] === "admin") {
        include __DIR__ . '/../views/templates/header_admin.php';
    } else {
        include __DIR__ . '/../views/templates/header_user.php';
    }
}


// Routing
switch ($page) {

    // --------------------------
    //  PUBLIC HOME PAGE
    // --------------------------
    case "home":
        $event->publicList();
        break;

    // --------------------------
    //  AUTHENTIFICATION
    // --------------------------
    case "login":
        $auth->loginPage();
        break;

    case "login_action":
        $auth->login();
        break;

    case "logout":
        $auth->logout();
        break;

    case "register":
        $auth->registerPage();
        break;

    case "register_action":
        $auth->register();
        break;

    case "register_success":
        $auth->registerSuccessPage();
        break;

    // --------------------------
    //  ADMIN DASHBOARD
    // --------------------------
    case "admin_dashboard":
        require_login();

        if ($_SESSION["user"]["role"] !== "admin") {
            echo "<h3>Accès refusé</h3>";
            exit();
        }

        $event->list();   // <-- Charge les événements + la vue !
        break;

    case "events_list":
        require_login();
        $event->list();
        break;

    case "calendar":
        require_login();
        $event->calendar();
        break;

    // --------------------------
    //  UTILISATEUR : événements
    // --------------------------
    case "user_events":
        require_login();
        $event->listForUsers();
        break;



    case "user_dashboard":
        require_login();
        include __DIR__ . '/../views/events/user_list.php';
        break;

    // --------------------------
    //  ÉVÉNEMENTS (CRUD ADMIN)
    // --------------------------
    case "event_add":
        require_login();
        $event->addPage();
        break;


    case "event_add_action":
        require_login();
        $event->add();
        break;

    case "event_edit":
        require_login();
        $id = $_GET['id'] ?? null;
        $event->edit($id);
        break;

    case "event_edit_action":
        require_login();
        $id = $_GET['id'] ?? null;
        $event->edit($id);
        break;

    case "event_delete":
        require_login();

        if (!isset($_GET['id'])) {
            die("Erreur : ID manquant pour la suppression.");
        }

        $event->delete($_GET['id']);
        break;


    // --------------------------
    //  INSCRIPTIONS
    // --------------------------
    case "inscrire":
        require_login();
        $inscription->inscrire();
        break;

    case "inscriptions_list":
        require_login();
        $inscription->liste();
        break;

    // --------------------------
    //  AJAX
    // --------------------------
    case "category_ajax_create":
        $category->addAjax();
        break;

    case "ajax_filter":
        $event->ajaxFilter();
        break;

    // --------------------------
    //  PAGE PAR DÉFAUT
    // --------------------------
    case "event_register":
        require_login();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $inscription->register($id);
        } else {
            header('Location: ?page=home');
        }
        break;

    case "cancel_registration":
        require_login();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $inscription->cancel($id);
        } else {
            header('Location: ?page=user_events');
        }
        break;

    default:
        $event->publicList();
        break;
}
