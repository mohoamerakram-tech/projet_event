<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success - ENSA Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        /* Animated Background */
        .auth-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(16, 185, 129, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(5, 150, 105, 0.3) 0%, transparent 50%);
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

        .success-card {
            background: white;
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
            z-index: 10;
            max-width: 480px;
            width: 100%;
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

        .success-icon {
            width: 80px;
            height: 80px;
            background: #d1fae5;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 24px;
        }

        .success-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 16px;
        }

        .success-message {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.4);
            color: white;
        }
    </style>
</head>

<body>
    <div class="auth-background"></div>
    <div class="auth-particles"></div>

    <div class="success-card">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        <h1 class="success-title">Account Created!</h1>
        <p class="success-message">
            Your account has been successfully created. You can now log in to explore events and join the community.
        </p>
        <a href="index.php?page=login" class="btn-login">
            <span>Go to Login</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</body>

</html>