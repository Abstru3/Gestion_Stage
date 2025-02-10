<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$isStudent = isset($_SESSION['role']) && $_SESSION['role'] === 'etudiant';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

// Vérifie si un ID est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Offre introuvable.");
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT o.*, e.nom AS nom_entreprise, DATEDIFF(o.date_fin, o.date_debut) AS duree 
    FROM offres_stages o
    JOIN entreprises e ON o.entreprise_id = e.id
    WHERE o.id = ?
");
$stmt->execute([$id]);
$internship = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$internship) {
    die("Offre introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - <?php echo htmlspecialchars($internship['titre']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_stage_details.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <header>
        
    </header>

    <main>
        <h1><?php echo htmlspecialchars($internship['titre']); ?></h1>
        <section class="offer-details">
            <div class="offer-infos">
                <p><strong><i class="fas fa-building"></i> Entreprise :</strong> <?php echo htmlspecialchars($internship['nom_entreprise'] ?? 'Inconnue'); ?></p>
                <p><strong><i class="fas fa-info-circle"></i> Description :</strong> <?php echo nl2br(htmlspecialchars($internship['description'])); ?></p>
                <p><strong><i class="fas fa-calendar-alt"></i> Date de début :</strong> <?php echo date('d/m/Y', strtotime($internship['date_debut'])); ?></p>
                <p><strong><i class="fas fa-map-marker-alt"></i> Lieu :</strong> <?php echo !empty($internship['lieu']) ? htmlspecialchars($internship['lieu']) : 'Lieu non fourni'; ?></p>
                <p><strong><i class="fas fa-globe"></i> Mode :</strong> <?php echo htmlspecialchars($internship['mode_stage']); ?></p>
                <p><strong><i class="fas fa-hourglass-half"></i> Durée :</strong> 
                    <?php 
                    echo ($internship['duree'] !== null) ? $internship['duree'] . ' jours' : 'Non spécifiée'; 
                    ?>
                </p>
            </div>
            <?php if ($isStudent): ?>
            <form class="apply" action="/Gestion_Stage/app/views/internships/apply.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="offre_id" value="<?php echo $internship['id']; ?>">
                <label for="cv">CV (PDF) :</label>
                <input type="file" name="cv" accept=".pdf" required>
                <label for="lettre_motivation">Lettre de motivation (PDF) :</label>
                <input type="file" name="lettre_motivation" accept=".pdf" required>
                <button type="submit" class="btn btn-apply">Postuler</button>
            </form>
            <?php endif; ?>
        </section>
    </main>

    <a href="/Gestion_Stage/index.php" class="btn index-button"><i class="fas fa-arrow-left"></i> Retour au menu</a>
</body>
</html>