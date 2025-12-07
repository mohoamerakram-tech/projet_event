<?php
require_once __DIR__ . '/../models/Inscription.php';
require_once __DIR__ . '/../models/Evenement.php';

class InscriptionController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function register($eventId)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $user = $_SESSION['user'];
        $nom = $user['nom'] . ' ' . $user['prenom'];
        $email = $user['email'];

        $inscriptionModel = new Inscription($this->pdo);
        $result = $inscriptionModel->create($eventId, $nom, $email);

        if ($result) {
            // Success
            // Notify User
            require_once __DIR__ . '/../models/Notification.php';
            $notificationModel = new Notification($this->pdo);
            $eventModel = new Evenement($this->pdo);
            $event = $eventModel->getById($eventId);
            $eventTitle = $event ? $event['titre'] : 'Event';

            $notificationModel->create(
                $user['id'], // We need user ID in session. If not there, we have a problem. Session usually has id.
                "Registration Confirmed: You have successfully registered for <strong>" . htmlspecialchars($eventTitle) . "</strong>.",
                "success",
                $eventId
            );

            // ADMIN NOTIFICATION: Capacity Reached Check
            if ($event['capacite']) {
                $currentParticipants = $inscriptionModel->getByEventId($eventId); // Retrieve list to count
                if (count($currentParticipants) >= $event['capacite']) {
                    $notificationModel->createForAdmins(
                        "Capacity Reached: <strong>" . htmlspecialchars($eventTitle) . "</strong> is now full (" . $event['capacite'] . " participants).",
                        "warning",
                        $eventId
                    );
                }
            }

            header('Location: index.php?page=user_events&success=registered');
        } else {
            // Already registered or error
            header('Location: index.php?page=user_events&error=already_registered');
        }
        exit();
    }

    public function cancel($inscriptionId)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit();
        }

        $user = $_SESSION['user'];
        $email = $user['email'];

        $inscriptionModel = new Inscription($this->pdo);
        $result = $inscriptionModel->cancel($inscriptionId, $email);

        if ($result) {
            // Notify User
            require_once __DIR__ . '/../models/Notification.php';
            $notificationModel = new Notification($this->pdo);
            // We need event details for the message, but we only have inscriptionId.
            // Inscription::cancel deleted the row? Yes.
            // So we should have fetched it before? Or just say "Registration cancelled".
            // Let's keep it simple.

            // To get user ID, we have $_SESSION['user']['id'].
            if (isset($_SESSION['user']['id'])) {
                $notificationModel->create(
                    $_SESSION['user']['id'],
                    "Registration Cancelled: You have cancelled your registration.",
                    "info",
                    null
                );

                // ADMIN NOTIFICATION: User Cancelled
                $userName = $_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom'];
                $notificationModel->createForAdmins(
                    "Registration Cancelled: User <strong>" . htmlspecialchars($userName) . "</strong> cancelled a registration.",
                    "warning",
                    null
                );
            }

            header('Location: index.php?page=user_events&success=canceled');
        } else {
            header('Location: index.php?page=user_events&error=cancel_failed');
        }
        exit();
    }
    public function liste()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }

        $inscriptionModel = new Inscription($this->pdo);
        $query = $_GET['q'] ?? null;

        if (isset($_GET['event_id'])) {
            // Detials Mode: Show participants for a specific event
            $eventId = $_GET['event_id'];
            $inscriptions = $inscriptionModel->getByEventId($eventId, $query);

            // Fetch event details for the header (get title from first result or separate query)
            // Using a separate query would be cleaner but for now extracting from first result or just passing ID is okay.
            // Better: use Evenement model to get title.
            require_once __DIR__ . '/../models/Evenement.php';
            $eventModel = new Evenement($this->pdo);
            $eventDetails = $eventModel->getById($eventId);

            $viewMode = 'details';
        } else {
            // Overview Mode: Show list of events with stats
            $eventsStats = $inscriptionModel->getEventStats($query);
            $viewMode = 'overview';
        }

        require __DIR__ . '/../views/inscriptions/list.php';
    }

    public function ajaxList()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            exit();
        }

        $inscriptionModel = new Inscription($this->pdo);
        $query = $_GET['q'] ?? null;
        $eventId = $_GET['event_id'] ?? null;
        $viewType = $_GET['view'] ?? 'list';

        if ($eventId) {
            // Details Mode
            $inscriptions = $inscriptionModel->getByEventId($eventId, $query);
            if (empty($inscriptions)) {
                if ($viewType === 'grid') {
                    echo '<div class="col-12 text-center p-5">
                            <div class="mb-3"><i class="bi bi-person-x text-muted" style="font-size: 3rem; opacity: 0.5;"></i></div>
                            <h6 class="fw-bold text-muted">No participants found</h6>
                           </div>';
                } else {
                    echo '<tr><td colspan="3" class="text-center p-4"><span class="text-muted">No participants found</span></td></tr>';
                }
            } else {
                if ($viewType === 'grid') {
                    // Grid View: Return Cards
                    foreach ($inscriptions as $i) {
                        $avatar = !empty($i['user_avatar']) ? htmlspecialchars($i['user_avatar']) : null;
                        $initials = strtoupper(substr($i['nom_participant'], 0, 1));

                        echo '<div class="col-md-6 col-lg-4 col-xl-3 animate-fade-in">
                                <div class="card h-100 border-0 shadow-sm glass-panel align-items-center text-center p-4" style="border-radius: 16px;">
                                    <div class="mb-3 position-relative">
                                        ' . ($avatar ?
                            '<img src="' . $avatar . '" class="rounded-circle object-fit-cover shadow-sm" style="width: 80px; height: 80px;" alt="' . htmlspecialchars($i['nom_participant']) . '">' :
                            '<div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">' . $initials . '</div>'
                        ) . '
                                        <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-white" style="width: 15px; height: 15px;"></div>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">' . htmlspecialchars($i['nom_participant']) . '</h5>
                                    <p class="text-muted small mb-3">' . htmlspecialchars($i['email_participant']) . '</p>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                        Confirmed
                                    </span>
                                </div>
                            </div>';
                    }
                } else {
                    // List View: Return Table Rows
                    foreach ($inscriptions as $i) {
                        $avatar = !empty($i['user_avatar']) ? htmlspecialchars($i['user_avatar']) : null;

                        echo '<tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    ' . ($avatar ?
                            '<img src="' . $avatar . '" class="rounded-circle object-fit-cover" style="width: 40px; height: 40px;" alt="Avatar">' :
                            '<div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 40px; height: 40px; font-size: 1.2rem;">' . strtoupper(substr($i['nom_participant'], 0, 1)) . '</div>'
                        ) . '
                                    <div class="fw-bold text-dark">' . htmlspecialchars($i['nom_participant']) . '</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-secondary fw-semibold">
                                ' . htmlspecialchars($i['email_participant']) . '
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">
                                    Confirmed
                                </span>
                            </td>
                        </tr>';
                    }
                }
            }
        } else {
            // Overview Mode
            $eventsStats = $inscriptionModel->getEventStats($query);
            if (empty($eventsStats)) {
                echo '<tr><td colspan="4" class="text-center p-4"><span class="text-muted">No events found</span></td></tr>';
            } else {
                foreach ($eventsStats as $stat) {
                    $image = !empty($stat['image']) ? htmlspecialchars($stat['image']) : 'default.jpg';
                    echo '<tr style="cursor: pointer;" onclick="window.location=\'?page=inscriptions_list&event_id=' . $stat['id'] . '\'">
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="uploads/' . $image . '" 
                                     alt="' . htmlspecialchars($stat['titre']) . '" 
                                     class="rounded-3 object-fit-cover" 
                                     style="width: 48px; height: 48px;">
                                <div>
                                    <div class="fw-bold text-dark">' . htmlspecialchars($stat['titre']) . '</div>
                                    <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>' . htmlspecialchars($stat['lieu']) . '</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary fw-semibold">
                            ' . date('d M Y', strtotime($stat['date_event'])) . '
                    </tr>';
                }
            }
        }
    }


    public function export($eventId)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }

        $inscriptionModel = new Inscription($this->pdo);
        $inscriptions = $inscriptionModel->getByEventId($eventId);

        // Fetch event title for filename
        $eventTitle = 'Event';
        if (!empty($inscriptions)) {
            $eventTitle = preg_replace('/[^a-zA-Z0-9]/', '_', $inscriptions[0]['event_title']);
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="participants_' . $eventTitle . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($output, ['Name', 'Email', 'Status']);

        foreach ($inscriptions as $row) {
            fputcsv($output, [
                $row['nom_participant'],
                $row['email_participant'],
                'Confirmed'
            ]);
        }

        fclose($output);
        exit();
    }
}
