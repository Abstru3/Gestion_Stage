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
    error_log("Erreur lors de la récupération des offres : " . $e->getMessage());
    $offres = [];
    $error_message = "Une erreur est survenue lors du chargement de vos offres.";
}

function formatDateFr($date) {
    if (empty($date)) return 'Non spécifiée';
    
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

function formatRemuneration($remuneration, $type_offre) {
    if (empty($remuneration)) return 'Non spécifiée';
    
    // Si c'est une alternance avec notation en pourcentage du SMIC
    if ($type_offre === 'alternance' && strpos($remuneration, 'smic') !== false) {
        switch ($remuneration) {
            case 'smic27': return '27% du SMIC';
            case 'smic43': return '43% du SMIC';
            case 'smic53': return '53% du SMIC';
            case 'smic100': return '100% du SMIC';
            default: return $remuneration;
        }
    } else {
        // Format monétaire standard pour les stages et les autres cas
        return number_format($remuneration, 0, ',', ' ') . ' €/mois';
    }
}

// Filtrer par type d'offre (stage ou alternance)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
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
            <h2>Mes offres</h2>
            <div class="button-group">
                <a href="/Gestion_Stage/app/views/internships/post_internship.php?type=stage" class="btn btn-primary">
                    <span class="icon">➕</span> Publier une offre de stage
                </a>
                <a href="/Gestion_Stage/app/views/internships/post_internship.php?type=alternance" class="btn btn-secondary">
                    <span class="icon">➕</span> Publier une offre d'alternance
                </a>
            </div>
        </div>
        
        <!-- Filtres pour les types d'offres -->
        <div class="filter-tabs">
            <a href="?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                Toutes les offres
            </a>
            <a href="?filter=stage" class="filter-tab <?= $filter === 'stage' ? 'active' : '' ?>">
                Stages
            </a>
            <a href="?filter=alternance" class="filter-tab <?= $filter === 'alternance' ? 'active' : '' ?>">
                Alternances
            </a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php elseif (empty($offres)): ?>
            <div class="empty-state">
                <p>Vous n'avez pas encore publié d'offres.</p>
                <p>Commencez par créer votre première offre !</p>
            </div>
        <?php else: ?>
            <div class="offers-grid">
                <?php foreach ($offres as $offre): 
                    // Filtrer selon le type sélectionné
                    if ($filter !== 'all' && $offre['type_offre'] !== $filter) {
                        continue;
                    }
                ?>
                    <div class="offer-card">
                        <div class="offer-header">
                            <h3>
                                <?= htmlspecialchars($offre['titre']) ?>
                                <span class="offer-type-badge <?= $offre['type_offre'] ?>">
                                    <?= $offre['type_offre'] === 'alternance' ? 'Alternance' : 'Stage' ?>
                                </span>
                            </h3>
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
                                <span>Fin: <?= $offre['date_fin'] ? formatDateFr($offre['date_fin']) : ($offre['type_offre'] === 'alternance' ? 'Selon contrat' : 'Non spécifiée') ?></span>
                            </div>

                            <?php if ($offre['remuneration']): ?>
                            <div class="detail-group">
                                <span class="icon">💰</span>
                                <span>Rémunération: <?= formatRemuneration($offre['remuneration'], $offre['type_offre']) ?></span>
                            </div>
                            
                            
                            
                            <?php endif; ?>
                        </div>

                        <!-- Interface améliorée pour les informations spécifiques à l'alternance -->
                        <?php if ($offre['type_offre'] === 'alternance'): ?>
                            <div class="alternance-details">
                                <h4><span class="icon">📋</span> Détails de l'alternance</h4>
                                
                                <div class="alternance-info-grid">
                                    <div class="alternance-info-item">
                                        <span class="icon">📄</span>
                                        <span class="label">Contrat:</span>
                                        <span class="value">
                                            <?= $offre['type_contrat'] === 'apprentissage' ? 'Apprentissage' : 'Professionnalisation' ?>
                                        </span>
                                    </div>
                                    
                                    <div class="alternance-info-item">
                                        <span class="icon">⏱️</span>
                                        <span class="label">Durée:</span>
                                        <span class="value">
                                            <?php
                                            if (!empty($offre['date_debut']) && !empty($offre['date_fin'])) {
                                                $date_debut = new DateTime($offre['date_debut']);
                                                $date_fin = new DateTime($offre['date_fin']);
                                                $interval = $date_debut->diff($date_fin);
                                                
                                                // Calculate total months (years * 12 + months)
                                                $months = $interval->y * 12 + $interval->m;
                                                
                                                // Add an additional month if there are more than 15 days
                                                if ($interval->d > 15) {
                                                    $months++;
                                                }
                                                
                                                echo $months . ' mois';
                                            } else {
                                                echo 'Selon contrat';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <div class="alternance-info-item">
                                        <span class="icon">🔄</span>
                                        <span class="label">Rythme:</span>
                                        <span class="value">
                                            <?php
                                            switch ($offre['rythme_alternance']) {
                                                case '1sem_1sem': echo "1 sem. entreprise / 1 sem. formation"; break;
                                                case '2sem_1sem': echo "2 sem. entreprise / 1 sem. formation"; break;
                                                case '3sem_1sem': echo "3 sem. entreprise / 1 sem. formation"; break;
                                                case '1mois_1sem': echo "1 mois entreprise / 1 sem. formation"; break;
                                                case 'autre': echo "Autre rythme"; break;
                                                default: echo $offre['rythme_alternance'];
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($offre['niveau_etude'])): ?>
                                    <div class="alternance-info-item">
                                        <span class="icon">🎓</span>
                                        <span class="label">Niveau requis:</span>
                                        <span class="value"><?= htmlspecialchars($offre['niveau_etude']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($offre['formation_visee'])): ?>
                                    <div class="alternance-info-item">
                                        <span class="icon">🏆</span>
                                        <span class="label">Formation visée:</span>
                                        <span class="value"><?= htmlspecialchars($offre['formation_visee']) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        

                        <div class="offer-description" data-description="<?= htmlspecialchars($offre['description']) ?>">
                            <h4>Description <?= $offre['type_offre'] === 'alternance' ? "de l'alternance" : "du stage" ?></h4>
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
