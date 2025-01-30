<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: login.php");
    exit();
}

$offre_id = $_GET['offre_id'] ?? 0;

$stmt = $pdo->prepare("SELECT c.*, e.nom, e.prenom FROM candidatures c JOIN etudiants e ON c.etudiant_id = e.id WHERE c.offre_id = ?");
$stmt->execute([$offre_id]);
$applications = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidature_id = $_POST['candidature_id'];
    $statut = $_POST['statut'];

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
    <title>Voir les candidatures</title>
    <link rel="stylesheet" href="css/style.css">
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
                <p>Statut actuel: <?php echo $application['statut']; ?></p>
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

    <p><a href="company_panel.php">Retour au panneau entreprise</a></p>
</body>
</html>