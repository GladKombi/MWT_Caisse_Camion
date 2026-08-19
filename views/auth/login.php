<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MWIRA_Trans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #16a34a;
            --dark-green: #14532d;
            --medium-green: #22c55e;
            --light-green: #86efac;
            --pale-green: #f0fdf4;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
            --gradient-green: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            --shadow-lg: 0 10px 30px rgba(22,163,74,0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 50%, #f0fdf4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            box-shadow: 0 10px 40px rgba(20, 83, 45, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.1);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--gradient-green);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.3);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 8px 20px rgba(22, 163, 74, 0.3);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 12px 30px rgba(22, 163, 74, 0.5);
            }
        }

        .logo-circle i {
            font-size: 2.5rem;
            color: white;
        }

        .brand-name {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--dark-green);
            margin-bottom: 0.25rem;
        }

        .brand-name span {
            color: var(--primary-green);
        }

        .brand-tagline {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.875rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: var(--gray-50);
        }

        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
            outline: none;
            background: var(--white);
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: var(--gray-600);
            padding: 0;
        }

        .password-toggle:hover {
            color: var(--primary-green);
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: var(--gradient-green);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            background: var(--dark-green);
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.3);
            transform: translateY(-2px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        .alert-error.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: var(--primary-green);
        }

        /* Mobile First */
        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }

            .logo-circle {
                width: 65px;
                height: 65px;
            }

            .logo-circle i {
                font-size: 2rem;
            }

            .brand-name {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <h1 class="brand-name">MWIRA<span>Trans</span></h1>
                <p class="brand-tagline">Gestion de Transport</p>
            </div>

            <!-- Error Message -->
            <div class="alert-error" id="errorMessage">
                <i class="bi bi-exclamation-circle me-2"></i>
                <span id="errorText">Matricule ou mot de passe incorrect</span>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="<?= htmlspecialchars(BASE_URL . '/login', ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-group">
                    <label class="form-label" for="matricule">Matricule</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="matricule" 
                            name="matricule" 
                            placeholder="Entrez votre matricule"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Mot de passe</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="Entrez votre mot de passe"
                            required
                        >
                        <button 
                            type="button" 
                            class="password-toggle" 
                            onclick="togglePassword()"
                            title="Afficher/Masquer le mot de passe"
                        >
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Se Connecter
                </button>
            </form>

            <!-- Back to Home -->
            <div class="back-link">
                <a href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>">
                    <i class="bi bi-arrow-left me-1"></i>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        // Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const matricule = document.getElementById('matricule').value.trim();
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            
            // Basic validation
            if (!matricule || !password) {
                errorText.textContent = 'Veuillez remplir tous les champs';
                errorMessage.classList.add('show');
                return;
            }
            
            if (password.length < 4) {
                errorText.textContent = 'Le mot de passe doit contenir au moins 4 caractères';
                errorMessage.classList.add('show');
                return;
            }
            
            // Hide error if validation passes
            errorMessage.classList.remove('show');
            
            // Here you can add your AJAX call or form submission
            // For now, we'll just submit the form
            this.submit();
        });

        // Clear error on input
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                document.getElementById('errorMessage').classList.remove('show');
            });
        });
    </script>
</body>
</html>
