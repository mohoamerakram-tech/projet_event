<?php
// Load admin header
load_header();

// Set page title variables
$pageTitle = "Mon Profil";
$pageSubtitle = "Gérer vos informations personnelles et paramètres";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - ENSA Events</title>

    <!-- Profile Page CSS -->
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header-card fade-in">
            <div class="profile-avatar-section">
                <div class="profile-avatar" style="position: relative; cursor: pointer;"
                    onclick="document.getElementById('avatar-input').click();">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 24px;">
                    <?php else: ?>
                        <?= strtoupper(substr($user['nom'] ?? 'A', 0, 1)) ?>
                    <?php endif; ?>
                    <div
                        style="position: absolute; bottom: -10px; right: -10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); border: 3px solid white;">
                        <i class="bi bi-camera-fill" style="color: white; font-size: 1rem;"></i>
                    </div>
                </div>
                <form id="avatar-form" action="?page=upload_avatar" method="POST" enctype="multipart/form-data"
                    style="display: none;">
                    <input type="file" id="avatar-input" name="avatar" accept="image/*"
                        onchange="document.getElementById('avatar-form').submit();">
                </form>
                <div class="profile-info">
                    <h1 class="profile-name"><?= htmlspecialchars($user['nom'] ?? 'Admin User') ?></h1>
                    <div class="profile-role">
                        <i class="bi bi-shield-check"></i>
                        <span>Administrateur</span>
                    </div>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['total_events'] ?? 0 ?></div>
                            <div class="stat-label">Événements créés</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['total_participants'] ?? 0 ?></div>
                            <div class="stat-label">Participants</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['upcoming_events'] ?? 0 ?></div>
                            <div class="stat-label">À venir</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['profile_success'])): ?>
            <div class="alert alert-success fade-in">
                <i class="bi bi-check-circle-fill"></i>
                <?= htmlspecialchars($_SESSION['profile_success']) ?>
            </div>
            <?php unset($_SESSION['profile_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['profile_errors'])): ?>
            <div class="alert alert-error fade-in">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= implode('<br>', array_map('htmlspecialchars', $_SESSION['profile_errors'])) ?>
            </div>
            <?php unset($_SESSION['profile_errors']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['password_success'])): ?>
            <div class="alert alert-success fade-in">
                <i class="bi bi-check-circle-fill"></i>
                <?= htmlspecialchars($_SESSION['password_success']) ?>
            </div>
            <?php unset($_SESSION['password_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['password_errors'])): ?>
            <div class="alert alert-error fade-in">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= implode('<br>', array_map('htmlspecialchars', $_SESSION['password_errors'])) ?>
            </div>
            <?php unset($_SESSION['password_errors']); ?>
        <?php endif; ?>

        <!-- Profile Grid -->
        <div class="profile-grid">
            <!-- Personal Information Card -->
            <div class="profile-card fade-in" style="animation-delay: 0.1s">
                <div class="profile-card-header">
                    <h2 class="profile-card-title">
                        <i class="bi bi-person-circle"></i>
                        Informations Personnelles
                    </h2>
                    <button class="edit-btn" onclick="toggleEditProfile()">
                        <i class="bi bi-pencil"></i> Modifier
                    </button>
                </div>

                <!-- Display Mode -->
                <div id="profile-display">
                    <div class="info-item">
                        <div class="info-label">Nom Complet</div>
                        <div class="info-value"><?= htmlspecialchars($user['nom'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($user['email'] ?? 'N/A') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Rôle</div>
                        <div class="info-value"><?= ucfirst(htmlspecialchars($user['role'] ?? 'N/A')) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Membre depuis</div>
                        <div class="info-value">
                            <?= isset($user['date_creation']) ? date('d F Y', strtotime($user['date_creation'])) : 'N/A' ?>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form method="POST" action="?page=admin_profile_update" id="profile-edit-form" class="profile-form">
                    <div class="form-group">
                        <label class="form-label">Nom Complet</label>
                        <input type="text" name="nom" class="form-control"
                            value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="toggleEditProfile()">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Card -->
            <div class="profile-card fade-in" style="animation-delay: 0.2s">
                <div class="profile-card-header">
                    <h2 class="profile-card-title">
                        <i class="bi bi-shield-lock"></i>
                        Sécurité
                    </h2>
                    <button class="edit-btn" onclick="toggleChangePassword()">
                        <i class="bi bi-key"></i> Modifier
                    </button>
                </div>

                <!-- Display Mode -->
                <div id="security-display">
                    <div class="info-item">
                        <div class="info-label">Mot de Passe</div>
                        <div class="info-value">••••••••••••</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dernière modification</div>
                        <div class="info-value">Il y a 30 jours</div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <form method="POST" action="?page=admin_password_change" id="password-form" class="profile-form">
                    <div class="form-group">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="new_password" class="form-control" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="toggleChangePassword()">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Modifier le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Activity Card (Full Width) -->
        <div class="profile-card fade-in" style="animation-delay: 0.3s">
            <div class="profile-card-header">
                <h2 class="profile-card-title">
                    <i class="bi bi-clock-history"></i>
                    Activité Récente
                </h2>
            </div>

            <?php if (empty($recentActivity)): ?>
                <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                    <i class="bi bi-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>Aucune activité récente</p>
                </div>
            <?php else: ?>
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-title"><?= htmlspecialchars($activity['titre']) ?></div>
                        <div class="activity-meta">
                            <span><i class="bi bi-calendar"></i> <?= date('d M Y', strtotime($activity['date_event'])) ?></span>
                            <span><i class="bi bi-tag"></i>
                                <?= htmlspecialchars($activity['category'] ?? 'Non catégorisé') ?></span>
                            <span><i class="bi bi-people"></i> <?= $activity['participants'] ?> participants</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleEditProfile() {
            const display = document.getElementById('profile-display');
            const form = document.getElementById('profile-edit-form');

            if (form.classList.contains('active')) {
                form.classList.remove('active');
                display.style.display = 'block';
            } else {
                form.classList.add('active');
                display.style.display = 'none';
            }
        }

        function toggleChangePassword() {
            const display = document.getElementById('security-display');
            const form = document.getElementById('password-form');

            if (form.classList.contains('active')) {
                form.classList.remove('active');
                display.style.display = 'block';
                form.reset();
            } else {
                form.classList.add('active');
                display.style.display = 'none';
            }
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>