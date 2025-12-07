<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events – Utilisateur</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Glass Theme -->
    <link rel="stylesheet" href="css/glass-theme.css">

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            window.markAsRead = async (id, element) => {
                if (event) event.stopPropagation();

                const notifBadge = document.getElementById('notification-count');
                const innerBadge = document.getElementById('inner-notification-count');
                const controls = document.getElementById('notification-controls'); // This might be null if not found

                // Safer selection for controls if ID fails (relative to button)
                const safeControls = controls || element.closest('#notification-controls') || element.closest('.d-flex');

                const notificationItem = element.closest('.notification-item');

                // OPTIMISTIC UI UPDATE
                if (notificationItem) {
                    notificationItem.style.opacity = '0.5';
                    notificationItem.classList.remove('bg-white');
                    notificationItem.classList.add('bg-light');
                    element.remove();
                }

                // Decrement badges
                let newCount = 0;
                if (notifBadge) {
                    let count = parseInt(notifBadge.textContent);
                    if (!isNaN(count) && count > 0) {
                        count--;
                        newCount = count;
                        // Only hide badge if count reaches 0
                        if (count === 0) {
                            notifBadge.style.display = 'none';
                            if (safeControls) safeControls.style.display = 'none';
                        } else {
                            notifBadge.textContent = count;
                        }
                    }
                }

                // Sync inner badge
                if (innerBadge && newCount > 0) {
                    innerBadge.textContent = newCount + ' New';
                }

                // Send request
                try {
                    const response = await fetch('index.php?page=notification_read&id=' + id);
                    const data = await response.json();
                    // Optionally sync with server response, but optimistic is usually enough
                } catch (e) {
                    console.error('Error marking as read', e);
                    //  location.reload(); // Fallback
                }
            }

            window.markAllAsRead = async (element) => {
                if (event) event.stopPropagation();

                const notifBadge = document.getElementById('notification-count');
                // Try ID first, then relative lookup
                let controls = document.getElementById('notification-controls');
                if (!controls) {
                    controls = element.closest('#notification-controls') || element.closest('div');
                }

                const allItems = document.querySelectorAll('.notification-item');

                // OPTIMISTIC UPDATE
                // 1. Hide badges immediately
                if (notifBadge) {
                    notifBadge.style.zIndex = '-1'; // Hack in case display none fails for some CSS reason
                    notifBadge.style.display = 'none';
                    notifBadge.textContent = '0';
                }
                if (controls) {
                    controls.style.display = 'none'; // This hides the container with "1 New" and the button
                }

                // 2. Mark all items as read visually
                allItems.forEach(item => {
                    item.style.opacity = '0.5';
                    item.classList.remove('bg-white');
                    item.classList.add('bg-light');
                });

                // 3. Remove "Mark all" button (redundant if parent is hidden, but safe)
                element.remove();

                // 4. Remove all individual "check" buttons
                document.querySelectorAll('button[title="Mark as read"]').forEach(btn => btn.remove());

                // 5. Send Request
                try {
                    await fetch('index.php?page=notification_read_all');
                } catch (e) {
                    console.error('Error marking all as read', e);
                }
            }
        });
    </script>
    <style>
        /* Premium Notification Styles */
        .notification-dropdown {
            width: 380px !important;
            border-radius: 20px !important;
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px) !important;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5) !important;
            animation: slideInDropdown 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            transform-origin: top right;
        }

        @keyframes slideInDropdown {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .notification-header {
            background: rgba(248, 250, 252, 0.8);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 16px 20px !important;
        }

        .notification-list {
            max-height: 400px !important;
            overflow-y: auto;
        }

        .notification-list::-webkit-scrollbar {
            width: 5px;
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .notification-item {
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.02);
            padding: 16px 20px !important;
            cursor: pointer;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.unread {
            background: rgba(99, 102, 241, 0.04);
            border-left: 3px solid var(--primary);
        }

        .notification-item:hover {
            background: rgba(0, 0, 0, 0.02) !important;
            transform: translateY(-1px);
        }

        .notification-item.read {
            opacity: 0.7;
        }

        .notif-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .mark-read-btn {
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.2s ease;
        }

        .notification-item:hover .mark-read-btn {
            opacity: 1;
            transform: translateX(0);
        }

        .mark-all-btn {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
            padding: 4px 12px;
            border-radius: 20px;
            transition: all 0.2s;
        }

        .mark-all-btn:hover {
            background: var(--primary);
            color: white;
        }
    </style>
</head>

<body>

    <!-- Floating Navigation -->
    <nav class="navbar navbar-expand-lg glass-panel navbar-floating">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php?page=home">
                <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px;">
                    <i class="bi bi-calendar-check-fill text-primary"></i>
                </div>
                <span class="fw-bold" style="font-size: 1.2rem; color: var(--dark);">ENSA Events</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-2">
                    <li class="nav-item">
                        <a href="index.php?page=home"
                            class="nav-link-custom <?= (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : '' ?>">
                            <i class="bi bi-compass me-1"></i> Découvrir
                        </a>
                    </li>
                    <li class="nav-item">
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                            <a href="index.php?page=admin_dashboard"
                                class="nav-link-custom <?= (isset($_GET['page']) && $_GET['page'] == 'admin_dashboard') ? 'active' : '' ?>">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        <?php else: ?>
                            <a href="index.php?page=user_events"
                                class="nav-link-custom <?= (isset($_GET['page']) && $_GET['page'] == 'user_events') ? 'active' : '' ?>">
                                <i class="bi bi-ticket-perforated me-1"></i> Mes Billets
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION["user"])): ?>
                        <!-- Notification Logic & UI -->
                        <?php
                        // Handle simple DB connection if not present
                        if (!isset($pdo)) {
                            if (isset($database)) {
                                $pdo = $database->getConnection();
                            } else {
                                // Try to locate db.php relatively
                                $dbPath = __DIR__ . '/../../config/db.php';
                                if (file_exists($dbPath)) {
                                    require_once $dbPath;
                                    $database = new Database();
                                    $pdo = $database->getConnection();
                                }
                            }
                        }

                        if (isset($pdo)) {
                            require_once __DIR__ . '/../../models/Notification.php';
                            require_once __DIR__ . '/../../models/Inscription.php';
                            
                            $notifModel = new Notification($pdo);
                            $inscriptionModel = new Inscription($pdo);
                            
                            // Check for reminders (events starting in < 1 hour)
                            // Ideally this should be a background job, but for this setup we do it on load
                            if (isset($_SESSION['user']['id'])) {
                                $upcomingReminders = $inscriptionModel->getUpcomingReminders(1); // 1 hour window
                                foreach ($upcomingReminders as $reminder) {
                                    if ($reminder['user_id'] == $_SESSION['user']['id']) {
                                        // Check if reminder already sent
                                        if (!$notifModel->exists($reminder['user_id'], $reminder['evenement_id'], 'reminder')) {
                                            $notifModel->create(
                                                $reminder['user_id'],
                                                "Reminder: <strong>" . htmlspecialchars($reminder['titre']) . "</strong> starts in less than an hour! (" . date('H:i', strtotime($reminder['heure'])) . ")",
                                                "reminder",
                                                $reminder['evenement_id']
                                            );
                                        }
                                    }
                                }
                            }

                            $unreadCount = $notifModel->getUnreadCount($_SESSION['user']['id']);
                            $notifications = $notifModel->getAllByUserId($_SESSION['user']['id'], 5);
                        } else {
                            $unreadCount = 0;
                            $notifications = [];
                        }
                        ?>
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center justify-content-center text-decoration-none text-dark position-relative me-3 rounded-circle bg-white shadow-sm transition-all hover-scale"
                                style="width: 42px; height: 42px;"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell text-primary fs-5"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span id="notification-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white shadow-sm" style="font-size: 0.65rem;">
                                        <?= $unreadCount ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-0 notification-dropdown">
                                <div class="notification-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                        Notifications
                                    </h6>

                                    <?php if ($unreadCount > 0): ?>
                                        <div id="notification-controls">
                                            <button onclick="window.markAllAsRead(this)" class="btn btn-link text-decoration-none p-0 mark-all-btn" title="Mark all as read">
                                                Mark all read <i class="bi bi-check-all ms-1"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div id="notification-list" class="notification-list">
                                    <?php if (empty($notifications)): ?>
                                        <div class="d-flex flex-column align-items-center justify-content-center p-5 text-center" style="opacity: 0.6;">
                                            <div class="rounded-circle bg-light p-3 mb-3">
                                                <i class="bi bi-bell-slash text-muted fs-3"></i>
                                            </div>
                                            <p class="text-dark fw-medium mb-1">All caught up!</p>
                                            <p class="text-muted small mb-0">No new notifications for now.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($notifications as $notif): ?>
                                            <?php
                                            $iconClass = 'info-circle-fill';
                                            $bgClass = 'primary';

                                            if ($notif['type'] == 'success') {
                                                $iconClass = 'check-circle-fill';
                                                $bgClass = 'success';
                                            }
                                            if ($notif['type'] == 'warning' || $notif['type'] == 'reminder') {
                                                $iconClass = 'exclamation-triangle-fill';
                                                $bgClass = 'warning';
                                            }
                                            if ($notif['type'] == 'danger') {
                                                $iconClass = 'x-circle-fill';
                                                $bgClass = 'danger';
                                            }
                                            ?>
                                            <div class="notification-item <?= $notif['is_read'] == 0 ? 'unread' : 'read' ?> position-relative">
                                                <div class="d-flex gap-3 align-items-start">
                                                    <div class="notif-icon-box bg-<?= $bgClass ?> bg-opacity-10 text-<?= $bgClass ?>">
                                                        <i class="bi bi-<?= $iconClass ?>"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="mb-1 text-dark" style="font-size: 0.9rem; line-height: 1.4;"><?= $notif['message'] ?></p>
                                                        <small class="text-muted fw-medium" style="font-size: 0.75rem;">
                                                            <i class="bi bi-clock me-1"></i><?= date('d M, H:i', strtotime($notif['created_at'])) ?>
                                                        </small>
                                                    </div>

                                                    <?php if ($notif['is_read'] == 0): ?>
                                                        <div class="d-flex flex-column align-items-end gap-2">
                                                            <button onclick="window.markAsRead(<?= $notif['id'] ?>, this)" class="mark-read-btn btn btn-sm btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" title="Mark as read">
                                                                <i class="bi bi-check text-primary"></i>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </ul>
                        </div>

                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <div class="user-avatar-small" style="<?= !empty($_SESSION['user']['avatar']) ? 'background: none;' : '' ?>">
                                    <?php if (!empty($_SESSION['user']['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($_SESSION['user']['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    <?php else: ?>
                                        <?= strtoupper(substr($_SESSION["user"]["nom"] ?? 'U', 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <span class="fw-semibold d-none d-md-block" style="color: var(--dark);">
                                    <?= htmlspecialchars($_SESSION["user"]["nom"] ?? 'User') ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2"
                                style="min-width: 200px;">
                                <li><a class="dropdown-item rounded-3 mb-1" href="#"><i class="bi bi-person me-2"></i> Mon
                                        Profil</a></li>
                                <li><a class="dropdown-item rounded-3 mb-1" href="#"><i class="bi bi-gear me-2"></i>
                                        Paramètres</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item rounded-3 text-danger" href="index.php?page=logout"><i
                                            class="bi bi-box-arrow-right me-2"></i> Déconnexion</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn-glass text-decoration-none">
                            Se connecter
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div style="height: 100px;"></div>

    <div class="container">