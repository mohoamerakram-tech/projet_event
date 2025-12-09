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
        function markAllNotificationsRead(e) {
            e.preventDefault();
            e.stopPropagation();

            fetch('index.php?page=notification_read_all')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(err => console.error(err));
        }

        function markNotificationRead(id, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            fetch('index.php?page=notification_read&id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optimistic update
                        const item = document.getElementById('notif-' + id);
                        if (item) {
                            // Find the button and replace with "Read" text
                            const btn = item.querySelector('button');
                            if (btn) {
                                btn.outerHTML = '<small class="text-muted fst-italic" style="font-size: 0.75rem;">Read</small>';
                            }
                            // Update badge count
                            const badges = document.querySelectorAll('.notification-badge');
                            badges.forEach(b => {
                                let count = parseInt(b.innerText);
                                if (count > 0) {
                                    count--;
                                    b.innerText = count;
                                    if (count === 0) b.style.display = 'none';
                                }
                            });
                        }
                    }
                })
                .catch(err => console.error(err));
        }
    </script>
    <style>
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }
    </style>
    <script>
        // Ensure functions are global
        // Moved functions to <script> block above
    </script>
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
                            <button
                                class="btn btn-light rounded-circle shadow-sm position-relative d-flex align-items-center justify-content-center"
                                style="width: 42px; height: 42px;" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell text-primary"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="notification-badge"><?= $unreadCount ?></span>
                                <?php endif; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0"
                                style="width: 350px; border-radius: 16px; overflow: hidden;">
                                <li class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark">Notifications</h6>
                                    <?php if ($unreadCount > 0): ?>
                                        <a href="#" onclick="markAllNotificationsRead(event)"
                                            class="small text-decoration-none fw-semibold">Mark all read</a>
                                    <?php endif; ?>
                                </li>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <?php if (empty($notifications)): ?>
                                        <li class="p-4 text-center text-muted">
                                            <i class="bi bi-bell-slash mb-2 fs-4 d-block opacity-50"></i>
                                            <small>No notifications</small>
                                        </li>
                                    <?php else: ?>
                                        <?php foreach ($notifications as $notif): ?>
                                            <li class="border-bottom position-relative hover-bg-light"
                                                id="notif-<?= $notif['id'] ?>">
                                                <div class="d-flex align-items-start gap-3 p-3 text-decoration-none text-dark">
                                                    <div class="flex-shrink-0 mt-1">
                                                        <?php
                                                        $icon = 'bi-info-circle';
                                                        $bgClass = 'bg-primary';
                                                        switch ($notif['type']) {
                                                            case 'success':
                                                                $icon = 'bi-check-circle';
                                                                $bgClass = 'bg-success';
                                                                break;
                                                            case 'warning':
                                                            case 'reminder':
                                                                $icon = 'bi-exclamation-triangle';
                                                                $bgClass = 'bg-warning';
                                                                break;
                                                            case 'danger':
                                                                $icon = 'bi-x-circle';
                                                                $bgClass = 'bg-danger';
                                                                break;
                                                        }
                                                        ?>
                                                        <div class="rounded-circle <?= $bgClass ?> bg-opacity-10 text-center d-flex align-items-center justify-content-center"
                                                            style="width: 36px; height: 36px;">
                                                            <i
                                                                class="bi <?= $icon ?> <?= str_replace('bg-', 'text-', $bgClass) ?>"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <p class="mb-1 small lh-sm">
                                                            <?= htmlspecialchars_decode($notif['message']) ?></p>
                                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                                <?= date('M d, H:i', strtotime($notif['created_at'])) ?>
                                                            </small>
                                                            <?php if (!$notif['is_read']): ?>
                                                                <button onclick="markNotificationRead(<?= $notif['id'] ?>, event)"
                                                                    class="btn btn-link btn-sm p-0 text-decoration-none text-primary"
                                                                    style="font-size: 0.8rem;">
                                                                    Mark as read
                                                                </button>
                                                            <?php else: ?>
                                                                <small class="text-muted fst-italic"
                                                                    style="font-size: 0.75rem;">Read</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </ul>
                        </div>

                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <div class="user-avatar-small"
                                    style="<?= !empty($_SESSION['user']['avatar']) ? 'background: none;' : '' ?>">
                                    <?php if (!empty($_SESSION['user']['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($_SESSION['user']['avatar']) ?>" alt="Avatar"
                                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
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
                                <li><a class="dropdown-item rounded-3 mb-1" href="index.php?page=user_profile"><i
                                            class="bi bi-person me-2"></i> Mon
                                        Profil</a></li>
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