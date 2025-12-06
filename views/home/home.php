<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENSA Events - Discover</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* Modern Tag Cloud */
        .tags-wrapper {
            position: relative;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 10px;
        }

        .tags-container {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            padding: 10px 5px;
            overflow: visible;
        }

        .tag-pill {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            color: var(--dark);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }

        .tag-pill:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
            background: white;
        }

        .tag-pill.active {
            background: var(--dark);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            border-color: transparent;
        }

        .tag-pill i {
            font-size: 1.1rem;
        }

        /* Search Input */
        .search-glass-container {
            max-width: 600px;
            margin: 0 auto 30px auto;
            position: relative;
        }

        .search-glass-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 15px 25px 15px 50px;
            font-size: 1.1rem;
            color: var(--dark);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .search-glass-input:focus {
            background: white;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            outline: none;
        }

        .search-icon-glass {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.2rem;
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <!-- Floating Navigation -->
    <?php include __DIR__ . '/../templates/header_user.php'; ?>

    <!-- Hero Section -->
    <section class="hero-glass text-center">
        <div class="hero-shape hero-shape-1 animate-float"></div>
        <div class="hero-shape hero-shape-2 animate-float-delayed"></div>

        <div class="container position-relative z-1">
            <span class="badge bg-white text-primary rounded-pill px-3 py-2 mb-4 shadow-sm animate-float">
                <i class="bi bi-stars me-2"></i> The Future of Campus Events
            </span>

            <h1 class="display-2 fw-black mb-4" style="font-weight: 900; letter-spacing: -2px;">
                Discover. Connect. <br>
                <span
                    style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Experience.
                </span>
            </h1>

            <p class="lead text-muted mb-5 mx-auto" style="max-width: 600px;">
                Join the most vibrant community of students and creators. Find workshops, hackathons, and parties
                happening right now.
            </p>

            <div class="d-flex justify-content-center gap-3">
                <a href="#events-section" class="btn btn-glass btn-lg px-5 py-3">
                    Explore Events
                </a>
                <?php if (!isset($_SESSION['user'])): ?>
                    <a href="index.php?page=register"
                        class="btn btn-light btn-lg px-5 py-3 rounded-pill shadow-sm text-primary fw-bold">
                        Join Now
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-panel p-4 text-center h-100 transition-hover">
                        <i class="bi bi-calendar-event text-primary mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <h2 class="fw-bold"><?= count($evenements) ?>+</h2>
                        <p class="text-muted mb-0">Active Events</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-panel p-4 text-center h-100 transition-hover">
                        <i class="bi bi-people text-secondary mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <h2 class="fw-bold">1000+</h2>
                        <p class="text-muted mb-0">Community Members</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-panel p-4 text-center h-100 transition-hover">
                        <i class="bi bi-trophy text-warning mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <h2 class="fw-bold"><?= count($categories) ?></h2>
                        <p class="text-muted mb-0">Event Categories</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content with Filters & Search -->
    <section id="events-section" class="py-5">
        <div class="container">

            <!-- Search Bar -->
            <div class="search-glass-container">
                <i class="bi bi-search search-icon-glass"></i>
                <input type="text" id="eventSearch" class="search-glass-input" placeholder="Search events, locations, topics...">
            </div>

            <!-- Tags/Horizontal Categories -->
            <div class="mb-5 text-center">
                <div class="tags-container justify-content-center">
                    <!-- All Tag -->
                    <a href="#" class="tag-pill active" data-id="">
                        <i class="bi bi-grid-fill"></i> All
                    </a>

                    <!-- Category Tags -->
                    <?php foreach ($categories as $cat): ?>
                        <a href="#" class="tag-pill" data-id="<?= $cat['id'] ?>">
                            <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($cat['nom']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Events Grid (Updated via AJAX) -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="events-container">
                <?php if (empty($evenements)): ?>
                    <div class="col-12 text-center py-5 glass-panel">
                        <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
                        <h3>No events found</h3>
                        <p class="text-muted">Try adjusting your filters or check back later.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($evenements as $e): ?>
                        <div class="col animate-fade-in">
                            <div class="glass-panel event-card-glass">
                                <div class="event-card-img-wrapper">
                                    <span class="event-date-badge-glass">
                                        <?= date('d M', strtotime($e['date_event'])) ?>
                                    </span>
                                    <img src="uploads/<?= !empty($e['image']) ? $e['image'] : 'default.jpg' ?>"
                                        class="event-card-img" alt="<?= htmlspecialchars($e['titre']) ?>">

                                    <div class="event-quick-view">
                                        <button class="btn btn-light rounded-pill fw-bold px-4 shadow-lg transform-scale"
                                            data-bs-toggle="modal" data-bs-target="#homeEventModal<?= $e['id'] ?>">
                                            <i class="bi bi-eye me-2"></i> View Details
                                        </button>
                                    </div>
                                </div>

                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                            <?= htmlspecialchars($e['category_name'] ?? 'Event') ?>
                                        </span>
                                    </div>

                                    <h4 class="fw-bold mb-2"><?= htmlspecialchars($e['titre']) ?></h4>

                                    <p class="text-muted small mb-4 flex-grow-1">
                                        <?= htmlspecialchars(substr($e['description'], 0, 80)) ?>...
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top border-light">
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                                            <?= htmlspecialchars($e['lieu']) ?>
                                        </div>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="bi bi-clock-fill me-1 text-warning"></i>
                                            <?= !empty($e['heure']) ? date('H:i', strtotime($e['heure'])) : 'TBA' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Home Event Modal (Must be included here for initial load) -->
                        <div class="modal fade" id="homeEventModal<?= $e['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden"
                                    style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px);">
                                    <div class="row g-0">
                                        <!-- Details Column (Left) -->
                                        <div class="col-lg-7 p-5 d-flex flex-column">
                                            <div class="d-flex align-items-center justify-content-between mb-4">
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                                    <?= htmlspecialchars($e['category_name'] ?? 'Event') ?>
                                                </span>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <h2 class="display-6 fw-bold mb-3"><?= htmlspecialchars($e['titre']) ?></h2>

                                            <div class="d-flex gap-4 mb-4 text-muted">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-calendar3 text-primary"></i>
                                                    <?= date('l, F d, Y', strtotime($e['date_event'])) ?>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-clock text-warning"></i>
                                                    <?= !empty($e['heure']) ? date('H:i', strtotime($e['heure'])) : 'TBA' ?>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <h5 class="fw-bold mb-2">About this event</h5>
                                                <p class="text-muted lh-lg">
                                                    <?= nl2br(htmlspecialchars($e['description'])) ?>
                                                </p>
                                            </div>

                                            <div class="mt-auto">
                                                <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3 bg-light">
                                                    <div class="rounded-circle bg-white p-2 shadow-sm text-danger">
                                                        <i class="bi bi-geo-alt-fill fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-0">Location</h6>
                                                        <p class="mb-0 text-muted small"><?= htmlspecialchars($e['lieu']) ?></p>
                                                    </div>
                                                </div>

                                                <?php if (isset($_SESSION['user'])): ?>
                                                    <a href="?page=event_register&id=<?= $e['id'] ?>"
                                                        class="btn btn-glass w-100 py-3 fw-bold shadow-sm">
                                                        <i class="bi bi-ticket-perforated me-2"></i> Register for Event
                                                    </a>
                                                <?php else: ?>
                                                    <a href="?page=login" class="btn btn-glass w-100 py-3 fw-bold shadow-sm">
                                                        <i class="bi bi-box-arrow-in-right me-2"></i> Login to Register
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Image Column (Right) -->
                                        <div class="col-lg-5 position-relative d-none d-lg-block">
                                            <img src="uploads/<?= !empty($e['image']) ? $e['image'] : 'default.jpg' ?>"
                                                class="w-100 h-100 object-fit-cover position-absolute top-0 start-0"
                                                alt="<?= htmlspecialchars($e['titre']) ?>">
                                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-to-r from-white via-transparent to-transparent"
                                                style="background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0) 20%);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer Spacer -->
    <div style="height: 100px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple scroll effect for navbar
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar-floating');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // AJAX Filtering Logic
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('eventSearch');
            const tags = document.querySelectorAll('.tag-pill');
            const container = document.getElementById('events-container');

            let activeCategoryId = '';

            // Initialize Favorites
            initFavorites();

            function initFavorites() {
                // Fetch user's favorites on load
                fetch('controllers/FavoriteController.php?action=list')
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            data.favorites.forEach(eventId => {
                                const btn = document.querySelector(`.btn-favorite[data-id="${eventId}"]`);
                                const icon = btn?.querySelector('i');
                                if (icon) {
                                    icon.classList.remove('bi-heart');
                                    icon.classList.add('bi-heart-fill');
                                    btn.classList.add('active');
                                }
                            });
                        }
                    })
                    .catch(console.error); // Silently fail if not logged in

                // Add click listeners to all favorite buttons (using event delegation for reliability)
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.btn-favorite');
                    if (!btn) return;

                    e.preventDefault();
                    e.stopPropagation();

                    const eventId = btn.getAttribute('data-id');
                    const icon = btn.querySelector('i');

                    fetch('controllers/FavoriteController.php?action=toggle', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                event_id: eventId
                            })
                        })
                        .then(r => {
                            if (r.status === 401) {
                                window.location.href = 'index.php?page=login';
                                return;
                            }
                            return r.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                if (data.is_favorite) {
                                    icon.classList.remove('bi-heart');
                                    icon.classList.add('bi-heart-fill');
                                    btn.classList.add('active');
                                } else {
                                    icon.classList.remove('bi-heart-fill');
                                    icon.classList.add('bi-heart');
                                    btn.classList.remove('active');
                                }
                            }
                        })
                        .catch(console.error);
                });
            }

            // 1. Tag Click Handling
            tags.forEach(tag => {
                tag.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Toggle active class
                    tags.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    activeCategoryId = this.getAttribute('data-id');
                    fetchEvents();
                });
            });

            // 2. Search Input Handling (Client-Side Tag Filtering Only)
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();

                // Filter Tags immediately
                tags.forEach(tag => {
                    const tagText = tag.textContent.trim().toLowerCase();
                    if (tagText.startsWith(query) || query === '') {
                        tag.style.display = 'inline-flex';
                    } else {
                        tag.style.display = 'none';
                    }
                });
            });

            // 3. Fetch Function
            function fetchEvents() {
                // Ignore search input for events, only use category
                const url = `index.php?page=ajax_filter&q=&category_id=${activeCategoryId}`;

                // Opacity feedback
                container.style.opacity = '0.5';

                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        container.style.opacity = '1';
                        // Re-initialize favorite buttons for new content
                        initFavorites();
                    })
                    .catch(err => {
                        console.error('Error fetching events:', err);
                        container.style.opacity = '1';
                    });
            }
        });
    </script>
</body>

</html>