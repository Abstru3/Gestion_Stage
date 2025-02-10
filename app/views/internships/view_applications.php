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

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidature_id = $_POST['candidature_id'];
    $action = $_POST['action'];

    if ($action == 'accepter') {
        $statut = 'acceptee';
    } elseif ($action == 'refuser') {
        $statut = 'refusee';
    }

    // Mettre à jour le statut
    $stmt = $pdo->prepare("UPDATE candidatures SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $candidature_id]);

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
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_view_applications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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
                <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                <p><strong><i class="fas fa-info-circle"></i> Statut actuel:</strong> <?php echo $application['statut']; ?></p>
                
                <!-- Affichage du CV -->
                <?php if (!empty($application['cv'])): ?>
                    <p><strong><i class="fas fa-file-alt"></i> CV:</strong> <a class="btn-CV" href="/Gestion_Stage/public/uploads/candidatures/<?php echo htmlspecialchars($application['cv']); ?>" target="_blank">Voir le CV</a></p>
                <?php else: ?>
                    <p><strong><i class="fas fa-file-alt"></i> CV:</strong> CV non fourni</p>
                <?php endif; ?>

                <!-- Affichage de la lettre de motivation -->
                <?php if (!empty($application['lettre_motivation'])): ?>
                    <p><strong><i class="fas fa-file-alt"></i> Lettre de motivation:</strong> <a class="btn-lettre" href="/Gestion_Stage/public/uploads/candidatures/<?php echo htmlspecialchars($application['lettre_motivation']); ?>" target="_blank">Voir la lettre</a></p>
                <?php else: ?>
                    <p><strong><i class="fas fa-file-alt"></i> Lettre de motivation:</strong> Lettre de motivation non fournie</p>
                <?php endif; ?>

                <form action="" method="post">
                    <input type="hidden" name="candidature_id" value="<?php echo $application['id']; ?>">
                    <button type="submit" name="action" value="accepter" class="btn-accept">Accepter</button>
                    <button type="submit" name="action" value="refuser" class="btn-reject">Refuser</button>
                </form>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a class="index-button" href="/Gestion_Stage/app/views/panels/company_panel.php">Retour au panneau entreprise</a></p>
</body>
</html>