<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once './app/config/database.php';
require_once './app/helpers/functions.php';
$total_companies = $pdo->query("SELECT COUNT(*) FROM entreprises")->fetchColumn();
$total_students = $pdo->query("SELECT COUNT(*) FROM etudiants WHERE role='etudiant'")->fetchColumn();
$total_internships = $pdo->query("SELECT COUNT(*) FROM offres_stages")->fetchColumn();

$recent_internships = get_internships($pdo);
$recent_internships = array_slice($recent_internships, 0, 4);
// Récupérer 4 entreprises vérifiées au hasard
$verified_companies = $pdo->query("
    SELECT id, nom, icone, description 
    FROM entreprises 
    WHERE valide = 1 
    ORDER BY RAND() 
    LIMIT 4
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Plateforme de stages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="./public/assets/css/style_accueil.css">
    <link rel="icon" type="image/png" href="./public/assets/images/logo_reduis.png">
</head>
<body>
    <?php if (isset($_SESSION['info_message'])): ?>
        <div class="info-message">
            <?= htmlspecialchars($_SESSION['info_message']) ?>
            <?php unset($_SESSION['info_message']); ?>
        </div>
    <?php endif; ?>
    <header class="main-header">
        <nav class="navbar">
            <div class="logo">
                <img src="./public/assets/images/logo.png" alt="NeversStage" height="100">
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="./app/views/home.php" class="btn btn-workspace"><i class="fas fa-user"></i> Mon Espace</a>
                    <a href="./app/views/auth/logout.php" class="btn btn-disconnect"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="./app/views/panels/admin_panel.php" class="btn btn-admin"><i class="fas fa-cogs"></i> Panel Admin</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="./app/views/auth/login.php" class="btn btn-login"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                    <a href="./app/views/auth/register.php" class="btn btn-register"><i class="fas fa-user-plus"></i> Inscription</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="hero-section">
            <h2>Trouvez le stage parfait pour votre avenir professionnel</h2>
            <p>Connectez-vous avec les meilleures entreprises et découvrez des opportunités exceptionnelles</p>
            <div class="search-box">
                <form id="search-form" action="/Gestion_Stage/app/views/internships/all_internships.php" method="get">
                    <input type="text" name="search" placeholder="Rechercher un stage..." id="search-input">
                    <input type="hidden" name="sort" value="date_debut">
                    <input type="hidden" name="order" value="ASC">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </header>

    <main>
        <section class="stats-section">
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <h3 id="total-internships"><?php echo $total_internships; ?></h3>
                <p>Offres de stages</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-building"></i>
                <h3 id="total-companies"><?php echo $total_companies; ?></h3>
                <p>Entreprises</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-graduate"></i>
                <h3 id="total-students"><?php echo $total_students; ?></h3>
                <p>Étudiants</p>
            </div>
        </section>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            function animateCounter(id, start, end, duration) {
                const element = document.getElementById(id);
                let startTime = null;

                function updateCounter(currentTime) {
                    if (!startTime) startTime = currentTime;
                    const progress = currentTime - startTime;
                    const value = Math.min(Math.floor(progress / duration * (end - start) + start), end);
                    element.textContent = value;
                    if (value < end) {
                        requestAnimationFrame(updateCounter);
                    }
                }

                requestAnimationFrame(updateCounter);
            }

            animateCounter('total-internships', 0, <?php echo $total_internships; ?>, 500);
            animateCounter('total-companies', 0, <?php echo $total_companies; ?>, 500);
            animateCounter('total-students', 0, <?php echo $total_students; ?>, 500);
        });
        </script>

        <section class="recent-offers">
            <h2>Dernières offres de stages</h2>
            <div class="offers-grid">
                <?php if (!empty($recent_internships)): ?>

                    <?php foreach ($recent_internships as $internship): ?>
                        <div class="offer-card">
                            <div class="offer-header">
                                <div class="mode-badge">
                                    <i class="fas <?php echo $internship['mode_stage'] === 'distanciel' ? 'fa-laptop-house' : 'fa-building'; ?>"></i>
                                    <?php echo htmlspecialchars($internship['mode_stage']); ?>
                                </div>
                                <div class="offer-header-content">
                                    <div>
                                        <h3><?php echo htmlspecialchars($internship['titre']); ?></h3>
                                        <span class="company-name">
                                            <?php echo htmlspecialchars($internship['nom_entreprise'] ?? 'Entreprise inconnue'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="offer-body">
                                <p><?php echo htmlspecialchars(substr($internship['description'], 0, 90)) . '...'; ?></p>
                                <div class="offer-details">
                                    <span><i class="fas fa-calendar-alt"></i> Début: <?php echo date('d/m/Y', strtotime($internship['date_debut'])); ?></span>
                                    <span><i class="fas fa-map-marker-alt"></i> Lieu: <?php echo !empty($internship['lieu']) ? htmlspecialchars($internship['lieu']) : 'Lieu non fourni'; ?></span>
                                    <span><i class="fas fa-clock"></i> Durée: <?php 
                                        echo calculateDuration($internship['date_debut'], $internship['date_fin']); 
                                    ?></span>
                                    <span><i class="fas fa-euro-sign"></i> Rémunération: <?php echo $internship['remuneration'] ? number_format($internship['remuneration'], 2, ',', ' ') . ' €' : 'Non spécifiée'; ?></span>
                                </div>
                            </div>
                            <div class="offer-footer">
                                <a href="/Gestion_Stage/app/views/internships/stage_details.php?id=<?php echo $internship['id']; ?>" class="btn btn-details">Voir plus</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune offre de stage disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        <div class="see-more">
        <a href="/Gestion_Stage/app/views/internships/all_internships.php" class="btn btn-primary">Voir toutes les offres</a>
        </div>
    </section>

    <section class="verified-companies">
        <h2><i class="fas fa-check-circle"></i> Entreprises vérifiées</h2>
        <div class="companies-grid">
            <?php foreach ($verified_companies as $company): ?>
                <div class="company-card">
                    <div class="company-logo">
                        <?php if (!empty($company['icone'])): ?>
                            <img src="/Gestion_Stage/public/uploads/profil/<?= htmlspecialchars($company['icone']) ?>" 
                                alt="Logo <?= htmlspecialchars($company['nom']) ?>">
                        <?php else: ?>
                            <i class="fas fa-building"></i>
                        <?php endif; ?>
                    </div>
                    <h3 class="company-title"><?= htmlspecialchars($company['nom']) ?></h3>
                    <p class="company-description"><?= htmlspecialchars(substr($company['description'], 0, 150)) ?>...</p>
                    <a href="/Gestion_Stage/app/views/company_profile.php?id=<?= $company['id'] ?>" 
                    class="btn btn-company">
                        Voir le profil
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    </main>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Informations</h3>
                <p>NeversStage est votre plateforme de référence pour trouver le stage qui correspond à vos aspirations professionnelles.</p>
            </div>
            <div class="footer-section">
                <h3>Liens rapides</h3>
                <ul>
                    <li><a href="stages.php">Offres de stages</a></li>
                    <li><a href="entreprises.php">Entreprises</a></li>
                    <li><a href="./app/views/a_propos.php">À propos</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p><i class="fas fa-envelope"></i> contact@neversstage.fr</p>
                <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
            </div>
            <div class="footer-section">
                <h3>Mentions légales</h3>
                <ul>
                    <li><a href="./public/policies/privacy.php">Politique de confidentialité</a></li>
                    <li><a href="./public/policies/terms.php">Conditions d'utilisation</a></li>
                    <li><a href="./public/policies/cookies.php">Politique des cookies</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> NeversStage - Tous droits réservés</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const offers = document.querySelectorAll('.offer-card');
            offers.forEach((offer, index) => {
                setTimeout(() => {
                    offer.classList.add('visible');
                }, index * 200);
            });
        });

    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.querySelector('.nav-links');

        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            this.classList.toggle('active');

            // Change l'icône
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Ferme le menu si on clique en dehors
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.nav-links') && 
                !event.target.closest('.mobile-menu-btn') && 
                navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initFloatingAnimation();
        });
        </script>

    <script src="./public/assets/js/script.js"></script>
    <script src="./public/assets/js/logo-animation.js"></script>
    <script src="./public/assets/js/float-animation.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function refreshVerifiedCompanies() {
            fetch('/Gestion_Stage/app/helpers/get_random_company.php')
                .then(response => response.text())
                .then(html => {
                    const companiesGrid = document.querySelector('.companies-grid');
                    companiesGrid.style.opacity = '0';
                    setTimeout(() => {
                        companiesGrid.innerHTML = html;
                        companiesGrid.style.opacity = '1';
                    }, 300);
                })
                .catch(error => console.error('Erreur:', error));
        }
        // Rafraîchir toutes les 10 secondes
        setInterval(refreshVerifiedCompanies, 10000);
    });
    </script>
</body>
</html>