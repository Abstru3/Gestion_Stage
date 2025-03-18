<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$isStudent = isset($_SESSION['role']) && $_SESSION['role'] === 'etudiant';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

// Variables pour les contrôles
$hasCV = false;
$alreadyApplied = false;
$applicationStatus = null;

if ($isStudent) {
    $stmt = $pdo->prepare("SELECT cv FROM etudiants WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $student = $stmt->fetch();
    $hasCV = !empty($student['cv']);
    
    // Vérifier si l'étudiant a déjà postulé à cette offre
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT statut FROM candidatures WHERE etudiant_id = ? AND offre_id = ?");
        $stmt->execute([$_SESSION['user_id'], $_GET['id']]);
        $application = $stmt->fetch();
        
        if ($application) {
            $alreadyApplied = true;
            $applicationStatus = $application['statut'];
        }
    }
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /Gestion_Stage/index.php');
    exit();
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT o.*, e.nom AS nom_entreprise, e.description AS description_entreprise, 
           e.site_web, o.logo AS offre_logo, DATEDIFF(o.date_fin, o.date_debut) AS duree 
    FROM offres_stages o
    JOIN entreprises e ON o.entreprise_id = e.id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$internship = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$internship) {
    header('Location: /Gestion_Stage/index.php');
    exit();
}

// Calculer la durée en mois pour les alternances
$duree_mois = null;
if ($internship['type_offre'] === 'alternance' && !empty($internship['date_debut']) && !empty($internship['date_fin'])) {
    $date_debut = new DateTime($internship['date_debut']);
    $date_fin = new DateTime($internship['date_fin']);
    $interval = $date_debut->diff($date_fin);
    
    // Calculer le nombre total de mois (années * 12 + mois)
    $duree_mois = $interval->y * 12 + $interval->m;
    
    // Ajouter un mois supplémentaire si plus de 15 jours
    if ($interval->d > 15) {
        $duree_mois++;
    }
}

// Formatter la rémunération selon le type d'offre
function formatRemuneration($remuneration, $type_offre, $type_remuneration) {
    if (empty($remuneration)) return 'Non spécifiée';
    
    // Si c'est une alternance avec notation en pourcentage du SMIC
    if ($type_offre === 'alternance' && strpos($type_remuneration, 'smic') !== false) {
        switch ($type_remuneration) {
            case 'smic27': return '27% du SMIC';
            case 'smic43': return '43% du SMIC';
            case 'smic53': return '53% du SMIC';
            case 'smic100': return '100% du SMIC';
            default: return number_format($remuneration, 0, ',', ' ') . ' €/mois';
        }
    } else {
        // Format monétaire standard pour les stages et les autres cas
        return number_format($remuneration, 0, ',', ' ') . ' €/mois';
    }
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
<body class="<?php echo $internship['type_offre'] === 'alternance' ? 'alternance-mode' : ''; ?>">
    <div class="container">
        <nav class="breadcrumb">
            <a href="/Gestion_Stage/index.php"><i class="fas fa-home"></i> Accueil</a> >
            <span>Détails de <?php echo $internship['type_offre'] === 'alternance' ? "l'alternance" : "l'offre"; ?></span>
        </nav>

        <main class="offer-container">
            <div class="offer-header">
                <h1>
                    <?php echo htmlspecialchars($internship['titre']); ?>
                    <span class="offer-type-badge <?php echo $internship['type_offre']; ?>">
                        <?php echo $internship['type_offre'] === 'alternance' ? 'Alternance' : 'Stage'; ?>
                    </span>
                </h1>
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
                        <h2><i class="fas fa-info-circle"></i> Description de <?php echo $internship['type_offre'] === 'alternance' ? "l'alternance" : "l'offre"; ?></h2>
                        <div class="card-content">
                            <div class="description-text">
                                <?php echo nl2br(htmlspecialchars($internship['description'])); ?>
                            </div>
                        </div>
                    </div>

                    <?php if($internship['type_offre'] === 'alternance'): ?>
                    <!-- Section spécifique à l'alternance -->
                    <div class="alternance-details">
                        <h3><i class="fas fa-graduation-cap"></i> Détails de l'alternance</h3>
                        
                        <div class="alternance-info-grid">
                            <div class="alternance-info-item">
                                <i class="fas fa-file-contract"></i>
                                <span class="label">Type de contrat:</span>
                                <span class="value">
                                    <?php echo $internship['type_contrat'] === 'apprentissage' ? 'Apprentissage' : 'Professionnalisation'; ?>
                                </span>
                            </div>
                            
                            <?php if($duree_mois): ?>
                            <div class="alternance-info-item">
                                <i class="fas fa-hourglass-half"></i>
                                <span class="label">Durée:</span>
                                <span class="value"><?php echo $duree_mois; ?> mois</span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="alternance-info-item">
                                <i class="fas fa-sync-alt"></i>
                                <span class="label">Rythme:</span>
                                <span class="value">
                                    <?php
                                    switch ($internship['rythme_alternance']) {
                                        case '1sem_1sem': echo "1 sem. entreprise / 1 sem. formation"; break;
                                        case '2sem_1sem': echo "2 sem. entreprise / 1 sem. formation"; break;
                                        case '3sem_1sem': echo "3 sem. entreprise / 1 sem. formation"; break;
                                        case '1mois_1sem': echo "1 mois entreprise / 1 sem. formation"; break;
                                        case 'autre': echo "Autre rythme"; break;
                                        default: echo htmlspecialchars($internship['rythme_alternance']);
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <?php if(!empty($internship['niveau_etude'])): ?>
                            <div class="alternance-info-item">
                                <i class="fas fa-user-graduate"></i>
                                <span class="label">Niveau requis:</span>
                                <span class="value"><?php echo htmlspecialchars($internship['niveau_etude']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($internship['formation_visee'])): ?>
                            <div class="alternance-info-item">
                                <i class="fas fa-award"></i>
                                <span class="label">Formation visée:</span>
                                <span class="value"><?php echo htmlspecialchars($internship['formation_visee']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($internship['ecole_partenaire'])): ?>
                            <div class="alternance-info-item">
                                <i class="fas fa-university"></i>
                                <span class="label">École partenaire:</span>
                                <span class="value"><?php echo htmlspecialchars($internship['ecole_partenaire']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <h2><i class="fas fa-building"></i> À propos de l'entreprise</h2>
                        <div class="card-content">
                            <?php echo nl2br(htmlspecialchars($internship['description_entreprise'] ?? '')); ?>
                            <div class="company-links">
                                <?php if (!empty($internship['site_web'])): ?>
                                    <a href="<?php echo htmlspecialchars($internship['site_web']); ?>" 
                                       target="_blank" 
                                       class="btn btn-link">
                                        <i class="fas fa-globe"></i> Visiter le site web
                                    </a>
                                <?php endif; ?>
                                <a href="/Gestion_Stage/app/views/company_profile.php?id=<?php echo $internship['entreprise_id']; ?>" 
                                   class="btn btn-link">
                                    <i class="fas fa-building"></i> Voir le profil de l'entreprise
                                </a>
                            </div>
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
                            <?php if(!empty($internship['date_fin'])): ?>
                            <li>
                                <i class="fas fa-calendar-check"></i>
                                <span>Fin : <?php echo date('d/m/Y', strtotime($internship['date_fin'])); ?></span>
                            </li>
                            <?php endif; ?>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Durée : 
                                    <?php 
                                    if($internship['type_offre'] === 'alternance' && $duree_mois) {
                                        echo $duree_mois . ' mois';
                                    } else {
                                        echo $internship['duree'] . ' jours'; 
                                    }
                                    ?>
                                </span>
                            </li>
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Lieu : <?php echo htmlspecialchars($internship['ville'] . ' (' . $internship['code_postal'] . ')'); ?></span>
                            </li>
                            <li>
                                <i class="fas fa-euro-sign"></i>
                                <span>Rémunération : <?php echo formatRemuneration($internship['remuneration'], $internship['type_offre'], $internship['type_remuneration']); ?></span>
                            </li>
                            <li>
                                <i class="fas fa-laptop-house"></i>
                                <span>Mode : <?php echo htmlspecialchars($internship['mode_stage']); ?></span>
                            </li>
                            <?php if($internship['type_offre'] === 'alternance'): ?>
                            <li>
                                <i class="fas fa-file-contract"></i>
                                <span>Type : <?php echo $internship['type_contrat'] === 'apprentissage' ? 'Contrat d\'apprentissage' : 'Contrat de professionnalisation'; ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php if ($isStudent): ?>
    <div class="card application-card">
        <h2><i class="fas fa-paper-plane"></i> Postuler</h2>
        <?php if ($alreadyApplied): ?>
            <div class="alert <?php echo $applicationStatus === 'acceptee' ? 'alert-success' : ($applicationStatus === 'refusee' ? 'alert-danger' : 'alert-info'); ?>">
                <i class="fas <?php echo $applicationStatus === 'acceptee' ? 'fa-check-circle' : ($applicationStatus === 'refusee' ? 'fa-times-circle' : 'fa-info-circle'); ?>"></i>
                <p>
                    <?php if ($applicationStatus === 'acceptee'): ?>
                        Votre candidature a été acceptée !
                    <?php elseif ($applicationStatus === 'refusee'): ?>
                        Votre candidature a été refusée.
                    <?php else: ?>
                        Vous avez déjà postulé à cette offre. Votre candidature est en cours d'examen.
                    <?php endif; ?>
                </p>
                <a href="/Gestion_Stage/app/views/panels/student_panel.php" class="btn btn-primary">
                    <i></i> Voir mes candidatures
                </a>
            </div>
        <?php elseif (!$hasCV): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Vous devez d'abord ajouter votre CV dans votre profil pour pouvoir postuler.</p>
                <a href="/Gestion_Stage/app/views/profile.php" class="btn btn-primary">
                    <i class="fas fa-user"></i> Accéder à mon profil
                </a>
            </div>
        <?php else: ?>
            <form class="application-form" action="/Gestion_Stage/app/views/internships/apply.php" 
                  method="post" enctype="multipart/form-data">
                <input type="hidden" name="offre_id" value="<?php echo $internship['id']; ?>">
                
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
        <?php endif; ?>
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