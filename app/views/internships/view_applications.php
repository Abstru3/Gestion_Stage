<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Gestion_Stage/app/helpers/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: /Gestion_Stage/app/views/auth/login.php");
    exit();
}

$offre_id = $_GET['offre_id'] ?? 0;

// Récupérer les candidatures pour l'offre spécifiée
$stmt = $pdo->prepare("SELECT c.id, c.statut, c.cv, c.lettre_motivation, e.nom, e.prenom, e.email 
                      FROM candidatures c 
                      JOIN etudiants e ON c.etudiant_id = e.id 
                      WHERE c.offre_id = ?");
$stmt->execute([$offre_id]);
$applications = $stmt->fetchAll();

// Traitement du formulaire de mise à jour ou de suppression
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidature_id = $_POST['candidature_id'];
    $statut = $_POST['statut'];

    if ($statut == 'refusee') {
        // Récupérer les fichiers avant suppression
        $stmt = $pdo->prepare("SELECT cv, lettre_motivation FROM candidatures WHERE id = ?");
        $stmt->execute([$candidature_id]);
        $candidature = $stmt->fetch();

        // Supprimer les fichiers du serveur
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/Gestion_Stage/public/uploads/candidatures/";
        if ($candidature) {
            if ($candidature['cv'] && file_exists($upload_dir . $candidature['cv'])) {
                unlink($upload_dir . $candidature['cv']);
            }
            if ($candidature['lettre_motivation'] && file_exists($upload_dir . $candidature['lettre_motivation'])) {
                unlink($upload_dir . $candidature['lettre_motivation']);
            }
        }

        // Supprimer la candidature
        $stmt = $pdo->prepare("DELETE FROM candidatures WHERE id = ?");
        $stmt->execute([$candidature_id]);
    } else {
        // Mettre à jour le statut
        $stmt = $pdo->prepare("UPDATE candidatures SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $candidature_id]);
    }

    header("Location: view_applications.php?offre_id=$offre_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeversStage - Voir les candidatures</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="icon" type="image/png" href="../../../public/assets/images/logo_reduis.png">
</head>
<body>
    <h1>Candidatures pour l'offre de stage</h1>

    <?php if (empty($applications)): ?>
        <p>Aucune candidature pour cette offre de stage.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($applications as $application): ?>
            <li>
                <h3><?php echo htmlspecialchars($application['nom'] . ' ' . $application['prenom']); ?></h3>
                <p>Email: <?php echo htmlspecialchars($application['email']); ?></p>
                <p>Statut actuel: <?php echo $application['statut']; ?></p>
                
                <!-- Affichage du CV -->
                <?php if (!empty($application['cv'])): ?>
                    <p>CV: <a href="/Gestion_Stage/public/uploads/candidatures/<?php echo htmlspecialchars($application['cv']); ?>" target="_blank">Voir le CV</a></p>
                <?php else: ?>
                    <p>CV non fourni</p>
                <?php endif; ?>

                <!-- Affichage de la lettre de motivation -->
                <?php if (!empty($application['lettre_motivation'])): ?>
                    <p>Lettre de motivation: <a href="/Gestion_Stage/public/uploads/candidatures/<?php echo htmlspecialchars($application['lettre_motivation']); ?>" target="_blank">Voir la lettre</a></p>
                <?php else: ?>
                    <p>Lettre de motivation non fournie</p>
                <?php endif; ?>

                <form action="" method="post">
                    <input type="hidden" name="candidature_id" value="<?php echo $application['id']; ?>">
                    <select name="statut">
                        <option value="en_attente" <?php echo $application['statut'] == 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                        <option value="acceptee" <?php echo $application['statut'] == 'acceptee' ? 'selected' : ''; ?>>Acceptée</option>
                        <option value="refusee" <?php echo $application['statut'] == 'refusee' ? 'selected' : ''; ?>>Refusée</option>
                    </select>
                    <button type="submit">Mettre à jour le statut</button>
                </form>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a class="index-button" href="/Gestion_Stage/app/views/panels/company_panel.php">Retour au panneau entreprise</a></p>
</body>
</html>
