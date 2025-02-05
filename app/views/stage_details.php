<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

// Vérifie si un ID est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Offre introuvable.");
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT *, DATEDIFF(date_fin, date_debut) AS duree FROM offres_stages WHERE id = ?");
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
    <title><?php echo htmlspecialchars($internship['titre']); ?> - Détails du Stage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
</head>
<body>
    <header>
        <a href="/Gestion_Stage/index.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
    </header>

    <main>
        <section class="offer-details">
            <h1><?php echo htmlspecialchars($internship['titre']); ?></h1>
            <p><strong>Entreprise :</strong> <?php echo htmlspecialchars($internship['nom_entreprise'] ?? 'Inconnue'); ?></p>
            <p><strong>Description :</strong> <?php echo nl2br(htmlspecialchars($internship['description'])); ?></p>
            <p><strong>Date de début :</strong> <?php echo date('d/m/Y', strtotime($internship['date_debut'])); ?></p>
            <p><strong>Lieu :</strong> <?php echo htmlspecialchars($internship['lieu']); ?></p>
            <p><strong>Mode :</strong> <?php echo htmlspecialchars($internship['mode_stage']); ?></p>
            <p><strong>Durée :</strong> 
                <?php 
                echo ($internship['duree'] !== null) ? $internship['duree'] . ' jours' : 'Non spécifiée'; 
                ?>
            </p>
            <a href="postuler.php?id=<?php echo $internship['id']; ?>" class="btn btn-apply">Postuler</a>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> NeversStage - Tous droits réservés</p>
    </footer>
</body>
</html>
