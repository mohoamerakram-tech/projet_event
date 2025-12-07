<?php
$error = isset($_SESSION["register_error"]) ? $_SESSION["register_error"] : "";
unset($_SESSION["register_error"]);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ENSA Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            position: relative;
            overflow-y: auto;
            padding: 40px 0;
        }

        /* Animated Background */
        .auth-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.3) 0%, transparent 50%);
            z-index: 1;
        }

        .auth-particles {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                radial-gradient(2px 2px at 20% 30%, white, transparent),
                radial-gradient(2px 2px at 60% 70%, white, transparent),
                radial-gradient(1px 1px at 50% 50%, white, transparent),
                radial-gradient(1px 1px at 80% 10%, white, transparent);
            background-size: 200% 200%;
            opacity: 0.4;
            animation: particleFloat 20s ease-in-out infinite;
            z-index: 1;
        }

        @keyframes particleFloat {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(50px, 50px);
            }
        }

        .auth-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 20px;
            margin-bottom: 24px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
        }

        .auth-logo i {
            font-size: 2.5rem;
            color: white;
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: 1rem;
            color: #6b7280;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.25rem;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            color: #1f2937;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .form-input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .btn-success-gradient {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-success-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.4);
        }

        .btn-success-gradient:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 32px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .divider span {
            padding: 0 16px;
            color: #9ca3af;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
        }

        .auth-link {
            color: #10b981;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-link:hover {
            color: #059669;
        }

        .back-home {
            position: absolute;
            top: 24px;
            left: 24px;
            z-index: 20;
        }

        .back-home-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .back-home-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateX(-4px);
        }

        .alert-custom {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .alert-danger-custom {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success-custom {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 1.25rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #10b981;
        }

        .features-list {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e5e7eb;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .feature-icon {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <div class="auth-background"></div>
    <div class="auth-particles"></div>

    <div class="back-home">
        <a href="index.php?page=home" class="back-home-btn">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Home</span>
        </a>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join ENSA Events and discover amazing opportunities</p>
            </div>

            <?php if ($error): ?>
                <div class="alert-custom alert-danger-custom">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="index.php?page=register_action" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-center mb-4">
                            <div class="position-relative">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center overflow-hidden"
                                    style="width: 100px; height: 100px; border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                    <img id="avatarPreview" src="assets/images/default-avatar.png" onerror="this.src='https://via.placeholder.com/100?text=User'" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <label for="avatarInput" class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm p-2 cursor-pointer"
                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                    <i class="bi bi-camera-fill text-primary" style="font-size: 1rem;"></i>
                                </label>
                                <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <div class="form-input-wrapper">
                        <i class="bi bi-person-fill form-input-icon"></i>
                        <input type="text" name="nom" class="form-input" placeholder="John Doe" required
                            autocomplete="name">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="form-input-wrapper">
                        <i class="bi bi-envelope-fill form-input-icon"></i>
                        <input type="email" name="email" class="form-input" placeholder="your.email@example.com"
                            required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="form-input-wrapper">
                        <i class="bi bi-lock-fill form-input-icon"></i>
                        <input type="password" name="password" id="password" class="form-input"
                            placeholder="Create a strong password" required autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-success-gradient">
                    <span>Create Account</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="features-list">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-check"></i>
                    </div>
                    <span>Access to exclusive events and workshops</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-check"></i>
                    </div>
                    <span>Network with students and professionals</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="bi bi-check"></i>
                    </div>
                    <span>Get notified about upcoming events</span>
                </div>
            </div>

            <div class="divider">
                <span>or</span>
            </div>

            <div class="auth-footer">
                <p style="color: #6b7280; margin-bottom: 8px;">Already have an account?</p>
                <a href="index.php?page=login" class="auth-link">Sign in instead →</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-fill');
                toggleIcon.classList.add('bi-eye-slash-fill');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash-fill');
                toggleIcon.classList.add('bi-eye-fill');
            }
        }

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>