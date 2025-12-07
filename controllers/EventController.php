<?php
// controllers/EventController.php
require_once __DIR__ . '/../models/Evenement.php';
require_once __DIR__ . '/../models/Notification.php';


class EventController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Liste tous les événements
    public function index()
    {
        $evenementModel = new Evenement($this->pdo);
        $evenements = $evenementModel->getAll();
        require __DIR__ . '/../views/events/list.php'; // Vue pour afficher la liste
    }

    public function list()
    {
        $evenementModel = new Evenement($this->pdo);
        $evenements = $evenementModel->getAll();
        require_once __DIR__ . '/../models/Categorie.php';
        $categorieModel = new Categorie($this->pdo);
        $categories = $categorieModel->getAll();

        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $totalParticipants = $userModel->count();

        require __DIR__ . '/../views/events/list.php';
    }
    public function listForUsers()
    {
        // Ensure user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }

        require_once __DIR__ . '/../models/Inscription.php';
        $inscriptionModel = new Inscription($this->pdo);

        // Get events registered by the current user
        $evenements = $inscriptionModel->getByUserId($_SESSION['user']['email']);

        require __DIR__ . '/../views/events/user_list.php';
    }

    // Public landing page - no authentication required
    public function publicList()
    {
        $evenementModel = new Evenement($this->pdo);
        $evenements = $evenementModel->getAll(true);

        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $userCount = $userModel->count();

        require_once __DIR__ . '/../models/Categorie.php';
        $categorieModel = new Categorie($this->pdo);
        $categories = $categorieModel->getAll();

        // Filter by category if specified
        if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
            $selectedCategoryId = $_GET['category_id'];
            $evenements = array_filter($evenements, function ($event) use ($selectedCategoryId) {
                return isset($event['category_id']) && $event['category_id'] == $selectedCategoryId;
            });
        }

        // Check for user registrations if logged in
        $registeredEventIds = [];
        if (isset($_SESSION['user'])) {
            require_once __DIR__ . '/../models/Inscription.php';
            $inscriptionModel = new Inscription($this->pdo);
            $registrations = $inscriptionModel->getByUserId($_SESSION['user']['email']);
            $registeredEventIds = array_column($registrations, 'evenement_id');
        }

        require __DIR__ . '/../views/home/home.php';
    }

    public function ajaxFilter()
    {
        $query = $_GET['q'] ?? '';
        $categoryId = $_GET['category_id'] ?? '';

        $evenementModel = new Evenement($this->pdo);
        $evenements = $evenementModel->search($query, $categoryId, true);

        // Check for user registrations if logged in
        $registeredEventIds = [];
        if (isset($_SESSION['user'])) {
            require_once __DIR__ . '/../models/Inscription.php';
            $inscriptionModel = new Inscription($this->pdo);
            $registrations = $inscriptionModel->getByUserId($_SESSION['user']['email']);
            $registeredEventIds = array_column($registrations, 'evenement_id');
        }

        // Return only the loop content
        if (empty($evenements)) {
            echo '<div class="col-12 text-center py-5 glass-panel">
                    <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
                    <h3>No events found</h3>
                    <p class="text-muted">Try adjusting your filters or check back later.</p>
                  </div>';
        } else {
            foreach ($evenements as $e) {
                // Include the card template directly or reuse logic
                // For simplicity, I will duplicate the card HTML here to ensure it matches exactly
                // ideally we would put this in a partial view

                $date = date('d M', strtotime($e['date_event']));
                $image = !empty($e['image']) ? $e['image'] : 'default.jpg';
                $titre = htmlspecialchars($e['titre']);
                $category = htmlspecialchars($e['category_name'] ?? 'Event');
                $desc = htmlspecialchars(substr($e['description'], 0, 80)) . '...';
                $lieu = htmlspecialchars($e['lieu']);
                $heure = !empty($e['heure']) ? date('H:i', strtotime($e['heure'])) : 'TBA';

                $registerButton = '';
                if (isset($_SESSION['user'])) {
                    if (in_array($e['id'], $registeredEventIds)) {
                        $registerButton = '
                        <button class="btn btn-secondary w-100 py-3 fw-bold shadow-sm text-white" disabled style="background: #cbd5e1; border: none; opacity: 1; cursor: not-allowed; color: #64748b !important;">
                            <i class="bi bi-check-circle-fill me-2"></i> Already Registered
                        </button>';
                    } else {
                        $registerButton = '
                        <a href="?page=event_register&id=' . $e['id'] . '" class="btn btn-glass w-100 py-3 fw-bold shadow-sm">
                            <i class="bi bi-ticket-perforated me-2"></i> Register for Event
                        </a>';
                    }
                } else {
                    $registerButton = '
                    <a href="?page=login" class="btn btn-glass w-100 py-3 fw-bold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login to Register
                    </a>';
                }

                echo '
                <div class="col animate-fade-in">
                    <div class="glass-panel event-card-glass">
                        <div class="event-card-img-wrapper">
                            <span class="event-date-badge-glass">' . $date . '</span>
                            <img src="uploads/' . $image . '" class="event-card-img" alt="' . $titre . '">

                            <div class="event-quick-view">
                                <button class="btn btn-light rounded-pill fw-bold px-4 shadow-lg transform-scale"
                                    data-bs-toggle="modal" data-bs-target="#homeEventModal' . $e['id'] . '">
                                    <i class="bi bi-eye me-2"></i> View Details
                                </button>
                            </div>
                        </div>

                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                    ' . $category . '
                                </span>
                            </div>

                            <h4 class="fw-bold mb-2">' . $titre . '</h4>

                            <p class="text-muted small mb-4 flex-grow-1">
                                ' . $desc . '
                            </p>

                            <div class="d-flex align-items-center justify-content-between pt-3 border-top border-light">
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                                    ' . $lieu . '
                                </div>
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-clock-fill me-1 text-warning"></i>
                                    ' . $heure . '
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal -->
                 <div class="modal fade" id="homeEventModal' . $e['id'] . '" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden"
                            style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
                            <div class="row g-0">
                                <div class="col-lg-7 p-5 d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                            ' . $category . '
                                        </span>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <h2 class="display-6 fw-bold mb-3">' . $titre . '</h2>
                                    <div class="d-flex gap-4 mb-4 text-muted">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar3 text-primary"></i>
                                            ' . date('l, F d, Y', strtotime($e['date_event'])) . '
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-clock text-warning"></i>
                                            ' . $heure . '
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <h5 class="fw-bold mb-2">About this event</h5>
                                        <p class="text-muted lh-lg">' . nl2br(htmlspecialchars($e['description'])) . '</p>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3 bg-light">
                                            <div class="rounded-circle bg-white p-2 shadow-sm text-danger">
                                                <i class="bi bi-geo-alt-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0">Location</h6>
                                                <p class="mb-0 text-muted small">' . $lieu . '</p>
                                            </div>
                                        </div>
                                        ' . $registerButton . '
                                    </div>
                                </div>
                                <div class="col-lg-5 position-relative d-none d-lg-block">
                                    <img src="uploads/' . $image . '" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0" alt="' . $titre . '">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-to-r from-white via-transparent to-transparent"
                                        style="background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0) 20%);">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ';
            }
        }
    }




    public function calendar()
    {
        $evenementModel = new Evenement($this->pdo);
        $evenements = $evenementModel->getAll();
        require __DIR__ . '/../views/calendar/index.php';
    }

    // Affiche le formulaire et ajoute un nouvel événement
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $date_event = $_POST['date_event'] ?? '';
            $lieu = trim($_POST['lieu'] ?? '');
            $heure = $_POST['heure'] ?? null;
            $category_id = $_POST['category_id'] ?? null;

            // Image
            $image_name = null;

            if (!empty($_FILES['image']['name'])) {

                $upload_dir = __DIR__ . '/../public/uploads/';


                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $safe_name = time() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $safe_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_name = $safe_name;
                } else {
                    die("Erreur lors de l’upload de l’image.");
                }
            }

            // Appel modèle AVEC BON ORDRE
            $evenementModel = new Evenement($this->pdo);
            $evenementModel->create(
                $titre,
                $description,
                $date_event,
                $lieu,
                $heure,
                $image_name,
                $category_id
            );

            // Notify all users about the new event
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            $notificationModel = new Notification($this->pdo); // Pass PDO connection

            $allUsers = $userModel->getAll();
            foreach ($allUsers as $u) {
                // Skip if user is the admin who created it? No, notify everyone.
                $notificationModel->create(
                    $u['id'],
                    "New event added: <strong>" . htmlspecialchars($titre) . "</strong>. Check it out!",
                    "info",
                    null // We don't have the new event ID easily without modifying model, passing null is fine or we could query lastInsertId but simplistic is better.
                );
            }

            // Dynamic redirect
            $redirectUrl = '../public/index.php?page=admin_dashboard';
            if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
            }
            header("Location: " . $redirectUrl);
            exit();
        }

        require_once __DIR__ . '/../models/Categorie.php';
        $categorieModel = new Categorie($this->pdo);
        $categories = $categorieModel->getAll();

        require '../views/events/add.php';
    }



    public function addPage()
    {
        // Vérifie que la variable $pdo existe dans le contrôleur
        if (!isset($this->pdo)) {
            die("Erreur : PDO non défini dans le contrôleur.");
        }

        require_once __DIR__ . '/../models/Categorie.php';
        $categorieModel = new Categorie($this->pdo);
        $categories = $categorieModel->getAll();

        // Affiche le formulaire HTML pour ajouter un événement
        require '../views/events/add.php';
    }



    // Modifier un événement existant
    public function edit($id)
    {
        $evenementModel = new Evenement($this->pdo);
        $currentEvent = $evenementModel->getById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $date_event = $_POST['date_event'] ?? '';
            $lieu = $_POST['lieu'] ?? '';
            $heure = $_POST['heure'] ?? null;
            $category_id = $_POST['category_id'] ?? null;

            // Gestion de l'image
            $image_name = $currentEvent['image'] ?? null; // Garder l'ancienne image par défaut

            if (!empty($_FILES['image']['name'])) {
                $upload_dir = __DIR__ . '/../public/uploads/';

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $safe_name = time() . '_' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $safe_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_name = $safe_name;
                }
            }

            $evenementModel->update($id, $titre, $description, $date_event, $lieu, $heure, $image_name, $category_id);

            // Notify registered users about the update
            require_once __DIR__ . '/../models/Inscription.php';
            $inscriptionModel = new Inscription($this->pdo);
            $participants = $inscriptionModel->getByEventId($id);

            $notificationModel = new Notification($this->pdo);
            foreach ($participants as $p) {
                if (isset($p['user_id'])) {
                    $notificationModel->create(
                        $p['user_id'],
                        "Event Update: <strong>" . htmlspecialchars($titre) . "</strong> details have been changed.",
                        "warning",
                        $id
                    );
                }
            }

            // Dynamic redirect
            $redirectUrl = '../public/index.php?page=admin_dashboard';
            if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
            }
            header("Location: " . $redirectUrl);
            exit;
        }

        require_once __DIR__ . '/../models/Categorie.php';
        $categorieModel = new Categorie($this->pdo);
        $categories = $categorieModel->getAll();

        $evenement = $currentEvent;
        require '../views/events/edit.php'; // Formulaire modification
    }

    // Supprimer un événement
    public function delete($id)
    {
        $evenementModel = new Evenement($this->pdo);

        // Notify participants before deletion
        require_once __DIR__ . '/../models/Inscription.php';
        $inscriptionModel = new Inscription($this->pdo);
        $participants = $inscriptionModel->getByEventId($id);

        // Get event title for message (optional, but nice)
        $event = $evenementModel->getById($id);
        $eventTitle = $event ? $event['titre'] : 'Event';

        // Delete the event
        $evenementModel->delete($id);

        // Send notifications (must do BEFORE delete if we want to link event? No, event is gone. Link is null.)
        // Actually, we should notify BEFORE delete if we want keys to work, but keys are SET NULL on delete.
        // But the message persists.
        require_once __DIR__ . '/../models/Notification.php';
        $notificationModel = new Notification($this->pdo);

        foreach ($participants as $p) {
            if (isset($p['user_id'])) {
                $notificationModel->create(
                    $p['user_id'],
                    "Event Cancelled: <strong>" . htmlspecialchars($eventTitle) . "</strong> has been cancelled.",
                    "danger",
                    null
                );
            }
        }

        // Dynamic redirect based on referer
        $redirectUrl = '../public/index.php?page=admin_dashboard';
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            $redirectUrl = $_SERVER['HTTP_REFERER'];
        }
        header("Location: " . $redirectUrl);
        exit();
    }
}
