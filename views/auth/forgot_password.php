<?php
$error = isset($_SESSION["forgot_error"]) ? $_SESSION["forgot_error"] : "";
$success = isset($_SESSION["forgot_success"]) ? $_SESSION["forgot_success"] : "";
unset($_SESSION["forgot_error"]);
unset($_SESSION["forgot_success"]);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - ENSA Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
        }

        .auth-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 20px;
            display: block;
            text-align: center;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.2s;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <i class="bi bi-key-fill auth-icon"></i>
            <h3>Mot de passe oublié ?</h3>
            <p class="text-muted">Entrez votre email pour recevoir un lien de réinitialisation.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div><?= htmlspecialchars($success) ?></div>
            </div>
        <?php endif; ?>

        <form action="index.php?page=forgot_password_action" method="POST">
            <div class="mb-4">
                <label class="form-label fw-bold">Adresse Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control bg-light border-start-0"
                        placeholder="exemple@email.com" required>
                </div>
            </div>

            <button type="submit" class="btn-primary-gradient">
                Envoyer le lien
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="index.php?page=login" class="text-decoration-none text-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la connexion
            </a>
        </div>
    </div>
</body>

</html>