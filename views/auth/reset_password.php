<?php
$error = isset($_SESSION["reset_error"]) ? $_SESSION["reset_error"] : "";
unset($_SESSION["reset_error"]);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation - ENSA Events</title>
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
        }

        .password-strength {
            height: 4px;
            background: #e9ecef;
            margin-top: 5px;
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s, background-color 0.3s;
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
            <h3>Nouveau mot de passe</h3>
            <p class="text-muted">Choisissez un mot de passe sécurisé.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="index.php?page=reset_password_action" method="POST" id="resetForm">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Nouveau mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" required minlength="8">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <div class="form-text" id="passHelp">Minimum 8 caractères.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Confirmer le mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>
                <div id="matchError" class="text-danger small mt-1" style="display:none;">
                    Les mots de passe ne correspondent pas.
                </div>
            </div>

            <button type="submit" class="btn-primary-gradient">Valider</button>
        </form>
    </div>

    <script>
        function togglePass(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }

        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        const strengthBar = document.getElementById('strengthBar');
        const matchError = document.getElementById('matchError');

        password.addEventListener('input', function () {
            const val = this.value;
            let strength = 0;
            if (val.length >= 8) strength += 25;
            if (val.match(/[A-Z]/)) strength += 25;
            if (val.match(/[0-9]/)) strength += 25;
            if (val.match(/[^A-Za-z0-9]/)) strength += 25;

            strengthBar.style.width = strength + '%';
            if (strength < 50) strengthBar.style.backgroundColor = '#dc3545';
            else if (strength < 75) strengthBar.style.backgroundColor = '#ffc107';
            else strengthBar.style.backgroundColor = '#198754';
        });

        confirm.addEventListener('input', function () {
            if (this.value !== password.value) {
                matchError.style.display = 'block';
            } else {
                matchError.style.display = 'none';
            }
        });
    </script>
</body>

</html>