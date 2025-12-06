<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENSA Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark"
        style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="?page=home">
                <i class="bi bi-calendar-event-fill me-2" style="font-size: 1.5rem;"></i>
                <span style="font-weight: 700; font-size: 1.25rem;">ENSA Events</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="?page=home">
                            <i class="bi bi-house-fill me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-light btn-sm px-3" href="?page=login"
                            style="border-radius: 20px; font-weight: 600;">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-light btn-sm px-3" href="?page=register"
                            style="border-radius: 20px; font-weight: 600;">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>