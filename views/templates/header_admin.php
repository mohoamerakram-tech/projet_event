<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Admin Dashboard' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #6366f1;
            --secondary: #64748b;
            --success: #22c55e;
            --warning: #eab308;
            --danger: #ef4444;
            --dark: #0f172a;
            --light: #f8fafc;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            padding: 0;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 32px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-logo a {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo i {
            font-size: 2rem;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-logo span {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            padding: 24px 16px;
            flex: 1;
        }

        .nav-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            margin-bottom: 16px;
            padding-left: 12px;
            font-weight: 600;
        }

        .nav-item {
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transform: translateX(4px);
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0%, rgba(99, 102, 241, 0.1) 100%);
            color: #818cf8;
            border-left: 3px solid #6366f1;
        }

        .nav-link i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-user {
            margin-top: auto;
            padding: 20px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 1.1rem;
        }

        .user-info h6 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .user-info span {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* Main Content Styles */
        .admin-main {
            margin-left: var(--sidebar-width);
            padding: 32px;
            min-height: 100vh;
        }

        /* Topbar Styles */
        .admin-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: white;
            padding: 20px 32px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .topbar-title h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1f2937;
            margin: 0;
        }

        .topbar-title p {
            font-size: 0.95rem;
            color: #6b7280;
            margin: 4px 0 0;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-search {
            position: relative;
        }

        .topbar-search input {
            width: 300px;
            padding: 10px 16px 10px 40px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .topbar-search input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .topbar-search i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .topbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .topbar-icon-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid #e5e7eb;
            color: #64748b;
            position: relative;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .topbar-icon-btn:hover {
            border-color: #6366f1;
            color: #6366f1;
            background: #f5f3ff;
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.4);
            color: white;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card-admin {
            background: white;
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-admin:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card-icon.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .stat-card-icon.green {
            background: #f0fdf4;
            color: #22c55e;
        }

        .stat-card-icon.purple {
            background: #f5f3ff;
            color: #8b5cf6;
        }

        .stat-card-icon.orange {
            background: #fff7ed;
            color: #f97316;
        }

        .stat-card-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .stat-card-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-card-trend {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 12px;
        }

        .stat-card-trend.up {
            color: #22c55e;
        }

        .stat-card-trend.down {
            color: #ef4444;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-logo">
            <a href="index.php">
                <i class="bi bi-calendar-check-fill"></i>
                <span>ENSA Events</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Principal</div>
            <div class="nav-item">
                <a href="?page=admin_dashboard"
                    class="nav-link <?= (!isset($_GET['page']) || $_GET['page'] == 'admin_dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="?page=events_list"
                    class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'events_list') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-event"></i>
                    <span>Événements</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="?page=inscriptions_list"
                    class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'inscriptions_list') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>
                    <span>Inscriptions</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="?page=calendar"
                    class="nav-link <?= (isset($_GET['page']) && $_GET['page'] == 'calendar') ? 'active' : '' ?>">
                    <i class="bi bi-calendar3"></i>
                    <span>Calendrier</span>
                </a>
            </div>

            <div class="nav-label" style="margin-top: 24px;">Administration</div>
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-gear-fill"></i>
                    <span>Paramètres</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="?page=logout" class="nav-link" style="color: #ef4444;">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-user">
            <a href="#" class="user-profile">
                <div class="user-avatar" style="<?= !empty($_SESSION['user']['avatar']) ? 'background: none;' : '' ?>">
                    <?php if (!empty($_SESSION['user']['avatar'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['user']['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                    <?php else: ?>
                        <?= strtoupper(substr($_SESSION['user']['nom'] ?? 'A', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <h6><?= htmlspecialchars($_SESSION['user']['prenom'] ?? 'Admin') ?>
                        <?= htmlspecialchars($_SESSION['user']['nom'] ?? 'User') ?></h6>
                    <span>Administrateur</span>
                </div>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="topbar-title">
                <h1><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h1>
                <p><?= isset($pageSubtitle) ? $pageSubtitle : 'Welcome back, Admin' ?></p>
            </div>

            <div class="topbar-actions">
                <form class="topbar-search" method="GET" action="index.php">
                    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? 'admin_dashboard') ?>">
                    <?php if(isset($_GET['event_id'])): ?>
                        <input type="hidden" name="event_id" value="<?= htmlspecialchars($_GET['event_id']) ?>">
                    <?php endif; ?>
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" id="admin-search-input" placeholder="Search..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" autocomplete="off">
                </form>

                <?php
                // Fetch Notifications
                $unreadCount = 0;
                $notifications = [];
                if (isset($_SESSION['user']['id'])) {
                    // Ensure $pdo is available
                    if (!isset($pdo) && file_exists(__DIR__ . '/../../config/db.php')) {
                        require_once __DIR__ . '/../../config/db.php';
                        $database = new Database();
                        $pdo = $database->getConnection();
                    }
                    if (isset($pdo)) {
                        require_once __DIR__ . '/../../models/Notification.php';
                        $notifModel = new Notification($pdo);
                        $unreadCount = $notifModel->getUnreadCount($_SESSION['user']['id']);
                        $notifications = $notifModel->getAllByUserId($_SESSION['user']['id'], 5);
                    }
                }
                ?>

                <div class="dropdown">
                    <button class="topbar-icon-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0" style="width: 350px; border-radius: 16px; overflow: hidden;">
                        <li class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">Notifications</h6>
                            <?php if ($unreadCount > 0): ?>
                                <a href="#" onclick="markAllNotificationsRead(event)" class="small text-decoration-none fw-semibold">Mark all read</a>
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
                                    <li class="border-bottom position-relative hover-bg-light" id="notif-<?= $notif['id'] ?>">
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
                                                        $icon = 'bi-exclamation-triangle';
                                                        $bgClass = 'bg-warning';
                                                        break;
                                                    case 'danger':
                                                        $icon = 'bi-x-circle';
                                                        $bgClass = 'bg-danger';
                                                        break;
                                                }
                                                ?>
                                                <div class="rounded-circle <?= $bgClass ?> bg-opacity-10 text-center d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                    <i class="bi <?= $icon ?> <?= str_replace('bg-', 'text-', $bgClass) ?>"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 small lh-sm"><?= htmlspecialchars_decode($notif['message']) ?></p>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        <?= date('M d, H:i', strtotime($notif['created_at'])) ?>
                                                    </small>
                                                    <?php if (!$notif['is_read']): ?>
                                                        <button onclick="markNotificationRead(<?= $notif['id'] ?>, event)" class="btn btn-link btn-sm p-0 text-decoration-none text-primary" style="font-size: 0.8rem;">
                                                            Mark as read
                                                        </button>
                                                    <?php else: ?>
                                                        <small class="text-muted fst-italic" style="font-size: 0.75rem;">Read</small>
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

                <button class="topbar-btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>New Event</span>
                </button>
            </div>
        </div>

        <!-- MODAL AJOUTER (Global) -->
        <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="modal-header border-0 p-4"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white bg-opacity-25 p-2 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px;">
                                <i class="bi bi-plus-lg fs-4"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" id="addEventModalLabel">New Event</h5>
                                <p class="mb-0 small opacity-75">Create a new event for your community</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="?page=event_add_action" enctype="multipart/form-data">
                        <div class="modal-body p-4 p-lg-5">
                            <div class="row g-4">
                                <!-- Image Upload Section -->
                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small text-uppercase mb-2">Event
                                        Cover</label>
                                    <div class="upload-zone p-4 border-2 border-dashed rounded-4 text-center position-relative"
                                        style="border-color: #e5e7eb; background: #f9fafb; transition: all 0.3s ease;"
                                        id="dropZone">
                                        <input type="file" name="image"
                                            class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer"
                                            accept="image/*" onchange="previewImage(this, 'preview-add-global')"
                                            style="z-index: 10;">
                                        <div id="preview-add-global-container" class="d-none">
                                            <img id="preview-add-global" src="" alt="Preview"
                                                class="rounded-3 shadow-sm"
                                                style="max-height: 200px; object-fit: cover;">
                                            <div class="mt-2 text-primary small fw-semibold">Click to change image</div>
                                        </div>
                                        <div id="upload-placeholder-add-global">
                                            <div class="mb-3">
                                                <i class="bi bi-cloud-arrow-up text-primary"
                                                    style="font-size: 3rem; opacity: 0.5;"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark">Drop your image here, or browse</h6>
                                            <p class="text-muted small mb-0">Supports: JPG, PNG, WEBP</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-secondary small text-uppercase">Event
                                        Title</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                                class="bi bi-type-h1"></i></span>
                                        <input type="text" name="titre"
                                            class="form-control bg-light border-start-0 rounded-end-3 py-3 fw-semibold"
                                            placeholder="e.g., Annual Tech Conference 2024" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small text-uppercase">Category</label>
                                        <input type="hidden" name="category_id" id="selectedCategoryInput" required>
                                        
                                        <!-- Search Filter & Quick Add -->
                                        <div class="mb-3">
                                            <div class="d-flex gap-2">
                                                <div class="input-group input-group-sm flex-grow-1">
                                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                                    <input type="text" id="categorySearchInput" class="form-control bg-light border-start-0" 
                                                           placeholder="Filter categories..." 
                                                           onkeyup="filterCategories()">
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="toggleAddCategory()">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Quick Add Form -->
                                            <div id="addCategoryForm" class="mt-2 d-none">
                                                <div class="input-group input-group-sm">
                                                    <input type="text" id="newCategoryName" class="form-control" placeholder="New category name...">
                                                    <button type="button" class="btn btn-success" onclick="createNewCategory()">Save</button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2" id="categoryTagsContainer" style="max-height: 150px; overflow-y: auto;">
                                            <?php
                                            if (!isset($categories)) {
                                                if (isset($pdo)) {
                                                    require_once __DIR__ . '/../../models/Categorie.php';
                                                    $catModel = new Categorie($pdo);
                                                    $categories = $catModel->getAll();
                                                }
                                            }
                                            
                                            if (isset($categories)) {
                                                foreach ($categories as $cat) {
                                                    echo '<button type="button" class="btn btn-outline-primary rounded-pill btn-sm px-3 category-tag" 
                                                            data-id="' . $cat['id'] . '"
                                                            onclick="selectCategory(this, ' . $cat['id'] . ')">' . 
                                                            htmlspecialchars($cat['nom']) . 
                                                          '</button>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <script>
                                            function filterCategories() {
                                                let input = document.getElementById('categorySearchInput');
                                                let filter = input.value.toLowerCase();
                                                let tags = document.querySelectorAll('.category-tag');

                                                tags.forEach(tag => {
                                                    let text = tag.textContent || tag.innerText;
                                                    if (text.toLowerCase().indexOf(filter) > -1) {
                                                        tag.style.display = "";
                                                    } else {
                                                        tag.style.display = "none";
                                                    }
                                                });
                                            }

                                            function selectCategory(btn, id) {
                                                // Update hidden input
                                                document.getElementById('selectedCategoryInput').value = id;
                                                
                                                // Visual update
                                                document.querySelectorAll('.category-tag').forEach(b => {
                                                    b.classList.remove('active', 'bg-primary', 'text-white');
                                                    b.classList.add('btn-outline-primary');
                                                });
                                                
                                                btn.classList.remove('btn-outline-primary');
                                                btn.classList.add('active', 'bg-primary', 'text-white');
                                            }

                                            function toggleAddCategory() {
                                                let form = document.getElementById('addCategoryForm');
                                                form.classList.toggle('d-none');
                                                if(!form.classList.contains('d-none')) {
                                                    document.getElementById('newCategoryName').focus();
                                                }
                                            }

                                            function createNewCategory() {
                                                let nameInput = document.getElementById('newCategoryName');
                                                let name = nameInput.value.trim();
                                                if(!name) return;

                                                fetch('?page=category_ajax_create', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json'
                                                    },
                                                    body: JSON.stringify({ nom: name })
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if(data.success) {
                                                        // Create new button
                                                        let container = document.getElementById('categoryTagsContainer');
                                                        let btn = document.createElement('button');
                                                        btn.type = 'button';
                                                        btn.className = 'btn btn-outline-primary rounded-pill btn-sm px-3 category-tag';
                                                        btn.setAttribute('data-id', data.id);
                                                        btn.onclick = function() { selectCategory(this, data.id); };
                                                        btn.innerText = data.nom;
                                                        
                                                        // Add to container
                                                        container.appendChild(btn);
                                                        
                                                        // Select it
                                                        selectCategory(btn, data.id);
                                                        
                                                        // Clean up
                                                        nameInput.value = '';
                                                        toggleAddCategory();
                                                        
                                                        // Scroll to bottom
                                                        container.scrollTop = container.scrollHeight;
                                                    } else {
                                                        alert('Error: ' + data.message);
                                                    }
                                                })
                                                .catch(err => console.error(err));
                                            }
                                        </script>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small text-uppercase">Date</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                                class="bi bi-calendar-event"></i></span>
                                        <input type="date" name="date_event"
                                            class="form-control bg-light border-start-0 rounded-end-3 py-3" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small text-uppercase">Time</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                                class="bi bi-clock"></i></span>
                                        <input type="time" name="heure"
                                            class="form-control bg-light border-start-0 rounded-end-3 py-3">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label
                                        class="form-label fw-bold text-secondary small text-uppercase">Description</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                                class="bi bi-text-paragraph"></i></span>
                                        <textarea name="description"
                                            class="form-control bg-light border-start-0 rounded-end-3 py-3" rows="4"
                                            placeholder="Describe your event details..." required></textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label
                                        class="form-label fw-bold text-secondary small text-uppercase">Location</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light border-end-0 rounded-start-3 ps-3 text-muted"><i
                                                class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="lieu"
                                            class="form-control bg-light border-start-0 rounded-end-3 py-3"
                                            placeholder="e.g., Grand Hall, Building A" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small text-uppercase d-flex justify-content-between">
                                        Max Participants
                                        <span class="badge bg-primary rounded-pill" id="capacityValue">50</span>
                                    </label>
                                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                                        <i class="bi bi-person text-muted"></i>
                                        <input type="range" class="form-range" name="capacite" min="10" max="1000" step="10" value="50" id="capacityRange" oninput="document.getElementById('capacityValue').textContent = this.value">
                                        <i class="bi bi-people-fill text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 p-4 bg-light bg-opacity-50">
                            <button type="button" class="btn btn-light border-0 px-4 py-2 fw-semibold rounded-3"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit"
                                class="btn px-5 py-2 fw-bold text-white rounded-3 shadow-sm hover-lift"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="bi bi-plus-lg me-2"></i> Create Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function previewImage(input, previewId) {
                const container = document.getElementById(previewId + '-container');
                const placeholder = document.getElementById('upload-placeholder-' + (previewId.includes('edit') ? previewId.replace('preview-', '') : (previewId.includes('global') ? 'add-global' : 'add')));
                const preview = document.getElementById(previewId);

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        container.classList.remove('d-none');
                        if (placeholder) placeholder.classList.add('d-none');
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

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
                if(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                fetch('index.php?page=notification_read&id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optimistic update
                        const item = document.getElementById('notif-' + id);
                        if(item) {
                            // Find the button and replace with "Read" text
                            const btn = item.querySelector('button');
                            if(btn) {
                                btn.outerHTML = '<small class="text-muted fst-italic" style="font-size: 0.75rem;">Read</small>';
                            }
                            // Update badge count
                            const badges = document.querySelectorAll('.notification-badge');
                            badges.forEach(b => {
                                let count = parseInt(b.innerText);
                                if(count > 0) {
                                    count--;
                                    b.innerText = count;
                                    if(count === 0) b.style.display = 'none';
                                }
                            });
                        }
                    }
                })
                .catch(err => console.error(err));
            }
        </script>

        <style>
            .hover-lift {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .hover-lift:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2) !important;
            }

            .form-control:focus,
            .form-select:focus {
                box-shadow: none;
                border-color: #667eea;
                background-color: #fff !important;
            }

            .input-group-text {
                border-color: #dee2e6;
            }

            .form-control {
                border-color: #dee2e6;
            }

            .input-group:focus-within .input-group-text {
                border-color: #667eea;
                background-color: #fff !important;
                color: #667eea !important;
            }
        </style>