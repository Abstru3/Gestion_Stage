<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$isStudent = isset($_SESSION['role']) && $_SESSION['role'] === 'etudiant';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /Gestion_Stage/index.php');
    exit();
}

$id = (int) $_GET['id'];

// Dans la requête SQL, modifiez pour récupérer le logo de l'offre
$stmt = $pdo->prepare("
    SELECT o.*, e.nom AS nom_entreprise, e.description AS description_entreprise, 
           e.site_web, o.logo AS offre_logo, DATEDIFF(o.date_fin, o.date_debut) AS duree 
    FROM offres_stages o
    JOIN entreprises e ON o.entreprise_id = e.id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$internship = $stmt->fetch(PDO::FETCH_ASSOC);

// Supprimez ou commentez cette ligne de débogage
// var_dump($internship['offre_logo']);

if (!$internship) {
    header('Location: /Gestion_Stage/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - <?php echo htmlspecialchars($internship['titre']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css"> -->
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_stage_details.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <div class="container">
        <nav class="breadcrumb">
            <a href="/Gestion_Stage/index.php"><i class="fas fa-home"></i> Accueil</a> >
            <span>Détails du stage</span>
        </nav>

        <main class="offer-container">
            <div class="offer-header">
                <h1><?php echo htmlspecialchars($internship['titre']); ?></h1>
                <div class="company-badge">
                    <?php if (!empty($internship['offre_logo'])): ?>
                        <img src="/Gestion_Stage/public/uploads/logos/<?php echo htmlspecialchars(basename($internship['offre_logo'])); ?>" 
                             alt="Logo <?php echo htmlspecialchars($internship['nom_entreprise']); ?>" 
                             class="company-logo"
                             onerror="this.onerror=null; this.src='/Gestion_Stage/public/assets/images/default-company.png';">
                    <?php else: ?>
                        <div class="company-logo-placeholder">
                            <i class="fas fa-building"></i>
                        </div>
                    <?php endif; ?>
                    <span class="company-name"><?php echo htmlspecialchars($internship['nom_entreprise']); ?></span>
                </div>
            </div>

            <div class="offer-grid">
                <div class="offer-main">
                <div class="card">
                    <h2><i class="fas fa-info-circle"></i> Description du stage</h2>
                    <div class="card-content">
                        <div class="description-text">
                            <?php echo nl2br(htmlspecialchars($internship['description'])); ?>
                        </div>
                    </div>
                </div>

                    <div class="card">
                        <h2><i class="fas fa-building"></i> À propos de l'entreprise</h2>
                        <div class="card-content">
                            <p><?php echo nl2br(htmlspecialchars($internship['description_entreprise'] ?? '')); ?></p>
                            <?php if (!empty($internship['site_web'])): ?>
                                <a href="<?php echo htmlspecialchars($internship['site_web']); ?>" 
                                   target="_blank" 
                                   class="btn btn-link">
                                    <i class="fas fa-globe"></i> Visiter le site web
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <aside class="offer-sidebar">
                    <div class="card info-card">
                        <h2><i class="fas fa-clipboard-list"></i> Informations clés</h2>
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-calendar-alt"></i>
                                <span>Début : <?php echo date('d/m/Y', strtotime($internship['date_debut'])); ?></span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Durée : <?php echo $internship['duree'] . ' jours'; ?></span>
                            </li>
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Lieu : <?php echo htmlspecialchars($internship['ville'] . ' (' . $internship['code_postal'] . ')'); ?></span>
                            </li>
                            <li>
                                <i class="fas fa-euro-sign"></i>
                                <span>Rémunération : <?php echo htmlspecialchars($internship['remuneration']); ?> €/mois</span>
                            </li>
                            <li>
                                <i class="fas fa-laptop-house"></i>
                                <span>Mode : <?php echo htmlspecialchars($internship['mode_stage']); ?></span>
                            </li>
                        </ul>
                    </div>

                    <?php if ($isStudent): ?>
                    <div class="card application-card">
                        <h2><i class="fas fa-paper-plane"></i> Postuler</h2>
                        <form class="application-form" action="/Gestion_Stage/app/views/internships/apply.php" 
                              method="post" enctype="multipart/form-data">
                            <input type="hidden" name="offre_id" value="<?php echo $internship['id']; ?>">
                            
                            <div class="form-group">
                                <label for="cv">
                                    <i class="fas fa-file-pdf"></i> CV (PDF)
                                </label>
                                <input type="file" id="cv" name="cv" accept=".pdf" required>
                            </div>

                            <div class="form-group">
                                <label for="lettre_motivation">
                                    <i class="fas fa-file-alt"></i> Lettre de motivation (PDF)
                                </label>
                                <input type="file" id="lettre_motivation" name="lettre_motivation" accept=".pdf" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Envoyer ma candidature
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </aside>
            </div>

            <div class="navigation-buttons">
                <a href="/Gestion_Stage/app/views/internships/all_internships.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux offres
                </a>
            </div>
        </main>
    </div>
</body>
</html>