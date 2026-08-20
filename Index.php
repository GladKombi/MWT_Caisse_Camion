<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MWIRA_Trans - Gestion de Transport</title>
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
            --gradient-green: linear-gradient(135deg, #16a34a 0%, #22c55e 50%, #4ade80 100%);
            --gradient-dark: linear-gradient(135deg, #14532d 0%, #16a34a 100%);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(22,163,74,0.15);
            --shadow-lg: 0 10px 30px rgba(22,163,74,0.25);
            --shadow-xl: 0 20px 40px rgba(20,83,45,0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--white);
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navbar Mobile First */
        .navbar-custom {
            background: var(--white);
            padding: 0.75rem 0;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--primary-green);
            font-size: 1.5rem;
            text-decoration: none;
        }

        .navbar-brand span {
            color: var(--dark-green);
        }

        .nav-link-custom {
            color: var(--gray-600);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .nav-link-custom:hover {
            background: var(--pale-green);
            color: var(--primary-green);
        }

        .nav-link-custom.active {
            background: var(--primary-green);
            color: var(--white);
        }

        /* Hero Section Mobile First */
        .hero-section {
            background: var(--gradient-dark);
            position: relative;
            overflow: hidden;
            padding: 3rem 0;
            min-height: auto;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 6s ease-in-out infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.05));
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .hero-badge {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            display: inline-block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .hero-title {
            color: white;
            font-weight: 900;
            font-size: 2.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-title .highlight {
            color: var(--light-green);
            position: relative;
        }

        .hero-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--light-green);
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .hero-image-wrapper {
            position: relative;
            text-align: center;
        }

        .hero-image {
            max-width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Buttons */
        .btn-custom {
            background: var(--white);
            color: var(--primary-green);
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 2px solid var(--white);
        }

        .btn-custom:hover {
            background: transparent;
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn-custom-green {
            background: var(--primary-green);
            color: var(--white);
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 2px solid var(--primary-green);
        }

        .btn-custom-green:hover {
            background: var(--dark-green);
            border-color: var(--dark-green);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Login Cards */
        .login-section {
            padding: 3rem 0;
            background: var(--gray-50);
        }

        .section-title {
            font-weight: 800;
            color: var(--dark-green);
            font-size: 2rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-subtitle {
            color: var(--gray-600);
            text-align: center;
            margin-bottom: 3rem;
        }

        .login-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            height: 100%;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .login-card:hover {
            border-color: var(--primary-green);
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--gradient-green);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .login-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(360deg);
            box-shadow: var(--shadow-lg);
        }

        .icon-wrapper i {
            font-size: 2rem;
            color: white;
        }

        .login-card h3 {
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 0.5rem;
        }

        .login-card p {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }

        /* Feature Cards */
        .features-section {
            padding: 4rem 0;
            background: var(--white);
        }

        .feature-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            height: 100%;
            border: 1px solid var(--gray-100);
        }

        .feature-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .feature-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .feature-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .feature-card:hover .feature-image img {
            transform: scale(1.1);
        }

        .feature-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(20,83,45,0.7), transparent);
        }

        .feature-content {
            padding: 1.5rem;
        }

        .feature-tag {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: var(--primary-green);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
        }

        .feature-card h5 {
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list li {
            padding: 0.375rem 0;
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .feature-list li i {
            color: var(--primary-green);
            margin-right: 0.5rem;
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-green);
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .cta-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .cta-title {
            color: white;
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .cta-text {
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
        }

        /* Footer */
        .footer {
            background: var(--dark-green);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: var(--primary-green);
            transform: translateY(-3px);
        }

        /* Mobile First Media Queries */
        @media (max-width: 767px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .hero-stats {
                gap: 1rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .login-card {
                margin-bottom: 1rem;
            }
            
            .feature-card {
                margin-bottom: 1rem;
            }
            
            .social-links {
                justify-content: center;
                margin-top: 1rem;
            }
        }

        /* Tablet */
        @media (min-width: 768px) and (max-width: 991px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-section {
                padding: 4rem 0;
            }
        }

        /* Desktop */
        @media (min-width: 992px) {
            .hero-section {
                padding: 5rem 0;
                min-height: 80vh;
                display: flex;
                align-items: center;
            }
            
            .hero-title {
                font-size: 3.5rem;
            }
            
            .login-section {
                padding: 5rem 0;
            }
            
            .features-section {
                padding: 5rem 0;
            }
            
            .cta-section {
                padding: 5rem 0;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-truck me-2"></i>
                MWIRA<span>Trans</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link-custom active" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="#connexion">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <span class="hero-badge">
                        <i class="bi bi-star-fill me-2"></i>
                        Leader du transport à Butembo
                    </span>
                    <h1 class="hero-title">
                        Transport <span class="highlight">Fiable</span> et <span class="highlight">Sécurisé</span>
                    </h1>
                    <p class="hero-subtitle">
                        MWIRA_Trans, votre partenaire de confiance pour le transport de matériaux 
                        de construction et de marchandises. Notre flotte moderne et notre équipe 
                        expérimentée garantissent un service exceptionnel.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#connexion" class="btn-custom">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Se Connecter
                        </a>
                        <a href="#services" class="btn-custom" style="background: transparent; color: white;">
                            <i class="bi bi-arrow-down-circle"></i>
                            Découvrir
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Véhicules</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">1000+</div>
                            <div class="stat-label">Livraisons</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">99%</div>
                            <div class="stat-label">Satisfaction</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hero-image-wrapper">
                        <img src="photo/bene2.png" alt="Camion MWIRA_Trans" class="hero-image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Section -->
    <section class="login-section" id="connexion">
        <div class="container">
            <h2 class="section-title">Espace de Connexion</h2>
            <p class="section-subtitle">Choisissez votre espace pour accéder à votre tableau de bord</p>
            
            <div class="row g-4">
                <!-- Admin -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="login-card">
                        <div class="icon-wrapper">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h3>ADMIN</h3>
                        <p>Gestion complète du système</p>
                        <a href="login" class="btn-custom-green">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Connexion
                        </a>
                    </div>
                </div>
                
                <!-- Direction -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="login-card">
                        <div class="icon-wrapper">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3>CEO</h3>
                        <p>Supervision et rapports stratégiques</p>
                        <a href="login" class="btn-custom-green">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Connexion
                        </a>
                    </div>
                </div>
                
                <!-- Employé -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="login-card">
                        <div class="icon-wrapper">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h3>EMPLOYE</h3>
                        <p>Accès aux opérations autorisées</p>
                        <a href="login" class="btn-custom-green">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Connexion
                        </a>
                    </div>
                </div>

                <!-- Comptable -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="login-card">
                        <div class="icon-wrapper">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h3>COMPTABLE</h3>
                        <p>Gestion financière</p>
                        <a href="login" class="btn-custom-green">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Connexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="services">
        <div class="container">
            <h2 class="section-title">Nos Services</h2>
            <p class="section-subtitle">Des solutions complètes pour tous vos besoins de transport</p>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="feature-card">
                        <div class="feature-image">
                            <span class="feature-tag">Transport</span>
                            <img src="photo/Screenshot_20220907-204854.png" alt="Transport de matériaux">
                        </div>
                        <div class="feature-content">
                            <h5><i class="bi bi-truck me-2"></i>Transport de Matériaux</h5>
                            <p>
                                Spécialisés dans le transport de matériaux de construction : 
                                sable, briques, ciment, pierres et plus encore.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="feature-card">
                        <div class="feature-image">
                            <span class="feature-tag">Logistique</span>
                            <img src="photo/bene2.png" alt="Logistique complète">
                        </div>
                        <div class="feature-content">
                            <h5><i class="bi bi-geo-alt me-2"></i>Logistique Complète</h5>
                            <p>
                                Solutions logistiques sur mesure pour optimiser vos livraisons 
                                et garantir la sécurité de vos marchandises.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="feature-card">
                        <div class="feature-image">
                            <span class="feature-tag">Maintenance</span>
                            <img src="photo/Screenshot_20220724-000841.png" alt="Maintenance et servicing">
                        </div>
                        <div class="feature-content">
                            <h5><i class="bi bi-wrench-adjustable me-2"></i>Maintenance & Servicing</h5>
                            <ul class="feature-list">
                                <li><i class="bi bi-check-circle-fill"></i>Lavage hebdomadaire des véhicules</li>
                                <li><i class="bi bi-check-circle-fill"></i>Entretien mensuel complet</li>
                                <li><i class="bi bi-check-circle-fill"></i>Remplacement des pièces usées</li>
                                <li><i class="bi bi-check-circle-fill"></i>Diagnostic électronique</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Prêt à commencer ?</h2>
                <p class="cta-text">
                    Rejoignez les nombreuses entreprises qui nous font confiance pour leur transport
                </p>
                <a href="contact.php" class="btn-custom">
                    <i class="bi bi-headset"></i>
                    Contactez-nous
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12 text-center text-lg-start mb-3 mb-lg-0">
                    <p class="mb-2">
                        <i class="bi bi-truck me-2"></i>
                        <strong>MWIRA_Trans</strong>
                    </p>
                    <p class="mb-0" style="font-size: 0.875rem; opacity: 0.8;">
                        &copy; <?php echo date('Y'); ?> MWIRA_Trans - Tous droits réservés
                    </p>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="social-links">
                        <a href="https://mobile.facebook.com/photo.php?fbid=1573448029707322&id=100011264207963&set=a.139872809731525&eav=Afaog_E3uQFlUBJk5C33FBogT3SYxZU0RtBb_52_1puppsz4oRCQgjZ5lM20dNmam7k&paipv=0&source=11&refid=17" class="social-icon" target="_blank" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="mailto:gladkombigs@gmail.com" class="social-icon" title="Email">
                            <i class="bi bi-envelope-fill"></i>
                        </a>
                        <a href="tel:0997019883" class="social-icon" title="Téléphone">
                            <i class="bi bi-telephone-fill"></i>
                        </a>
                        <a href="https://wa.me/+243997019883" class="social-icon" target="_blank" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
