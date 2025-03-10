<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM offres_stages WHERE entreprise_id = ? ORDER BY date_publication DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $offres = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des offres de stage : " . $e->getMessage());
    $offres = [];
    $error_message = "Une erreur est survenue lors du chargement de vos offres de stage.";
}

function formatDateFr($date) {
    $mois = array(
        'January' => 'janvier',
        'February' => 'février',
        'March' => 'mars',
        'April' => 'avril',
        'May' => 'mai',
        'June' => 'juin',
        'July' => 'juillet',
        'August' => 'août',
        'September' => 'septembre',
        'October' => 'octobre',
        'November' => 'novembre',
        'December' => 'décembre'
    );
    
    $date = new DateTime($date);
    $dateEnglish = $date->format('d F Y');
    
    foreach($mois as $en => $fr) {
        $dateEnglish = str_replace($en, $fr, $dateEnglish);
    }
    
    return $dateEnglish;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Panneau Entreprise</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_company_panel.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panneau Entreprise</h1>
        </div>
    </header>
    
    <nav>
    <button class="menu-toggle" aria-label="Ouvrir le menu">☰</button>
    <ul class="menu">
            <li><a href="/Gestion_Stage/app/views/home.php">🏠 Mon espace</a></li>
            <li><a href="/Gestion_Stage/app/views/profile.php">👤 Mon profil</a></li>
            <li><a href="/Gestion_Stage/app/views/auth/logout.php">🚪 Déconnexion</a></li>
        </ul>
    </nav>

    <main class="container">
        <div class="header-actions">
            <h2>Mes offres de stages</h2>
            <a href="/Gestion_Stage/app/views/internships/post_internship.php" class="btn btn-primary">
                <span class="icon">➕</span> Publier une nouvelle offre
            </a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php elseif (empty($offres)): ?>
            <div class="empty-state">
                <p>Vous n'avez pas encore publié d'offres de stage.</p>
                <p>Commencez par créer votre première offre !</p>
            </div>
        <?php else: ?>
            <div class="offers-grid">
                <?php foreach ($offres as $offre): ?>
                    <div class="offer-card">
                        <div class="offer-header">
                            <h3><?= htmlspecialchars($offre['titre']) ?></h3>
                            <span class="offer-type"><?= $offre['mode_stage'] ?></span>
                        </div>
                        
                        <div class="offer-details">
                            <div class="detail-group">
                                <span class="icon">📍</span>
                                <span><?= htmlspecialchars($offre['ville']) ?>, <?= htmlspecialchars($offre['region']) ?></span>
                            </div>
                            
                            <div class="detail-group">
                                <span class="icon">📅</span>
                                <span>Début: <?= formatDateFr($offre['date_debut']) ?></span>
                            </div>
                            
                            <div class="detail-group">
                                <span class="icon">📅</span>
                                <span>Fin: <?= formatDateFr($offre['date_fin']) ?></span>
                            </div>

                            <?php if ($offre['remuneration']): ?>
                            <div class="detail-group">
                                <span class="icon">💰</span>
                                <span>Rémunération: <?= number_format($offre['remuneration'], 2, ',', ' ') ?> €/mois</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="offer-description" data-description="<?= htmlspecialchars($offre['description']) ?>">
                            <h4>Description du stage</h4>
                            <p><?= nl2br(htmlspecialchars(substr($offre['description'], 0, 250))) ?>...</p>
                            <button class="read-more">
                                <span>Voir plus</span>
                                <span class="icon">▼</span>
                            </button>
                        </div>

                        <div class="offer-actions">
                            <a class="btn btn-edit" href="/Gestion_Stage/app/views/internships/edit_internship.php?id=<?= $offre['id'] ?>">
                                <span class="icon">✏️</span> Modifier
                            </a>
                            <a class="btn btn-view" href="/Gestion_Stage/app/views/internships/view_applications.php?offre_id=<?= $offre['id'] ?>">
                                <span class="icon">👥</span> Voir les candidatures
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <a class="index-button" href="/Gestion_Stage/app/views/home.php">
            <span class="icon">←</span> Retour à l'espace personnel
        </a>
    </footer>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.offer-description').forEach(description => {
        const readMoreBtn = description.querySelector('.read-more');
        const paragraph = description.querySelector('p');
        const fullText = description.dataset.description;
        let isExpanded = false;

        readMoreBtn.addEventListener('click', () => {
            isExpanded = !isExpanded;
            
            if (isExpanded) {
                description.classList.add('expanded');
                paragraph.textContent = fullText;
                readMoreBtn.querySelector('span:first-child').textContent = 'Voir moins';
            } else {
                description.classList.remove('expanded');
                paragraph.textContent = fullText.substring(0, 250) + '...';
                readMoreBtn.querySelector('span:first-child').textContent = 'Voir plus';
            }
        });
    });
});
</script>
</body>
</html>
