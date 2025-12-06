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
                                    <?= htmlspecialchars($_SESSION["user"]["prenom"] ?? 'User') ?>
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