<?php
session_start();
require_once '../app/config/database.php';
require_once '../app/helpers/functions.php';
$recent_internships = get_internships($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stages Iut - Plateforme de stages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style_accueil.css">
</head>
<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="logo">
                <h1>Stage<span>Iut</span></h1>
            </div>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="home.php" class="nav-link"><i class="fas fa-user"></i> Mon Espace</a>
                    <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-login"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                    <a href="register.php" class="btn btn-register"><i class="fas fa-user-plus"></i> Inscription</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="hero-section">
            <h2>Trouvez le stage parfait pour votre avenir professionnel</h2>
            <p>Connectez-vous avec les meilleures entreprises et découvrez des opportunités exceptionnelles</p>
            <div class="search-box">
                <input type="text" placeholder="Rechercher un stage..." id="search-input">
                <button class="search-btn"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </header>

    <main>
        <section class="stats-section">
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <h3>500+</h3>
                <p>Offres de stages</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-building"></i>
                <h3>200+</h3>
                <p>Entreprises</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-graduate"></i>
                <h3>1000+</h3>
                <p>Étudiants</p>
            </div>
        </section>

        <section class="recent-offers">
            <h2>Dernières offres de stages</h2>
            <div class="offers-grid">
                <?php foreach ($recent_internships as $internship): ?>
                <div class="offer-card">
                    <div class="offer-header">
                        <h3><?php echo htmlspecialchars($internship['titre']); ?></h3>
                        <span class="company-name"><?php echo htmlspecialchars($internship['nom_entreprise']); ?></span>
                    </div>
                    <div class="offer-body">
                        <p><?php echo htmlspecialchars(substr($internship['description'], 0, 150)) . '...'; ?></p>
                        <div class="offer-details">
                            <span><i class="fas fa-calendar-alt"></i> Début: <?php echo date('d/m/Y', strtotime($internship['date_debut'])); ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($internship['lieu']); ?></span>
                        </div>
                    </div>
                    <div class="offer-footer">
                        <a href="stage_details.php?id=<?php echo $internship['id']; ?>" class="btn btn-details">Voir plus</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="see-more">
                <a href="stages.php" class="btn btn-primary">Voir toutes les offres</a>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>À propos</h3>
                <p>StagesIut est votre plateforme de référence pour trouver le stage qui correspond à vos aspirations professionnelles.</p>
            </div>
            <div class="footer-section">
                <h3>Liens rapides</h3>
                <ul>
                    <li><a href="stages.php">Offres de stages</a></li>
                    <li><a href="entreprises.php">Entreprises</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p><i class="fas fa-envelope"></i> contact@stagespro.fr</p>
                <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> StagesIut - Tous droits réservés</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>