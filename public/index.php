<?php
session_start();

// Charger les contrôleurs
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/InscriptionController.php';
require_once __DIR__ . '/../controllers/NotificationController.php';

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
    //  MOT DE PASSE OUBLIÉ
    // --------------------------
    case "forgot_password":
        require_once __DIR__ . '/../controllers/ForgotPasswordController.php';
        $forgotPassword = new ForgotPasswordController();
        $forgotPassword->forgotPasswordPage();
        break;

    case "forgot_password_action":
        require_once __DIR__ . '/../controllers/ForgotPasswordController.php';
        $forgotPassword = new ForgotPasswordController();
        $forgotPassword->processForgotPassword();
        break;

    case "reset_password":
        require_once __DIR__ . '/../controllers/ForgotPasswordController.php';
        $forgotPassword = new ForgotPasswordController();
        $forgotPassword->resetPasswordPage();
        break;

    case "reset_password_action":
        require_once __DIR__ . '/../controllers/ForgotPasswordController.php';
        $forgotPassword = new ForgotPasswordController();
        $forgotPassword->processResetPassword();
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

        require_once __DIR__ . '/../controllers/DashboardController.php';
        $dashboard = new DashboardController($pdo);
        $dashboard->index();
        break;

    // Dashboard API endpoints (AJAX)
    case "dashboard_stats":
        require_login();
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $dashboard = new DashboardController($pdo);
        $dashboard->getStats();
        break;

    case "dashboard_events_month":
        require_login();
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $dashboard = new DashboardController($pdo);
        $dashboard->getEventsPerMonth();
        break;

    case "dashboard_categories":
        require_login();
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $dashboard = new DashboardController($pdo);
        $dashboard->getCategoryDistribution();
        break;

    case "dashboard_participants":
        require_login();
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $dashboard = new DashboardController($pdo);
        $dashboard->getParticipantsGrowth();
        break;

    case "dashboard_latest":
        require_login();
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $dashboard = new DashboardController($pdo);
        $dashboard->getLatestEvents();
        break;

    case "dashboard_upcoming":
        require_login();
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $dashboard = new DashboardController($pdo);
        $dashboard->getUpcomingEvents();
        break;

    case "dashboard_top":
        require_login();
        require_once __DIR__ . '/../controllers/DashboardController.php';
        $dashboard = new DashboardController($pdo);
        $dashboard->getTopEvents();
        break;

    // --------------------------
    //  ADMIN PROFILE
    // --------------------------
    case "admin_profile":
        require_login();
        require_once __DIR__ . '/../controllers/ProfileController.php';
        $profile = new ProfileController($pdo);
        $profile->adminProfile();
        break;

    case "admin_profile_update":
        require_login();
        require_once __DIR__ . '/../controllers/ProfileController.php';
        $profile = new ProfileController($pdo);
        $profile->updateProfile();
        break;

    case "admin_password_change":
        require_login();
        require_once __DIR__ . '/../controllers/ProfileController.php';
        $profile = new ProfileController($pdo);
        $profile->changePassword();
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

    case "inscriptions_ajax":
        // require_login is handled inside ajaxList for cleaner partial return or we can do it here.
        // It's safer to check login here too or rely on controller.
        // Controller checks generic admin role, but let's allow it to reach controller.
        $inscription->ajaxList();
        break;

    case "inscriptions_export":
        // require_login handled in controller
        if (isset($_GET['event_id'])) {
            $inscription->export($_GET['event_id']);
        } else {
            header('Location: index.php?page=inscriptions_list');
            exit();
        }
        break;

    case "notification_read":
        // Access via AJAX
        require_once __DIR__ . '/../controllers/NotificationController.php';
        $notify = new NotificationController($pdo);
        $notify->markAsRead();
        break;

    case "notification_read_all":
        require_once __DIR__ . '/../controllers/NotificationController.php';
        $notify = new NotificationController($pdo);
        $notify->markAllAsRead();
        break;

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

    // --------------------------
    //  USER PROFILE
    // --------------------------
    case "user_profile":
        require_login();
        require_once __DIR__ . '/../controllers/ProfileController.php';
        $profile = new ProfileController($pdo);
        $profile->userProfile();
        break;

    case "user_profile_update":
        require_login();
        require_once __DIR__ . '/../controllers/ProfileController.php';
        $profile = new ProfileController($pdo);
        $profile->updateUserProfile();
        break;

    case "user_password_change":
        require_login();
        require_once __DIR__ . '/../controllers/ProfileController.php';
        $profile = new ProfileController($pdo);
        $profile->changePassword();
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
