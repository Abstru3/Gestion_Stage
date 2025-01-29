<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'entreprise') {
    header("Location: login.php");
    exit();
}

$entreprise_id = $_SESSION['user_id'];

// Supprimer une offre
if (isset($_POST['delete_offre'])) {
    $offre_id = $_POST['offre_id'];
    $stmt = $pdo->prepare("DELETE FROM offres_stages WHERE id = ? AND entreprise_id = ?");
    $stmt->execute([$offre_id, $entreprise_id]);
}

// Récupérer les offres de l'entreprise
$stmt = $pdo->prepare("SELECT * FROM offres_stages WHERE entreprise_id = ? ORDER BY date_debut DESC");
$stmt->execute([$entreprise_id]);
$internships = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau Entreprise</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panneau Entreprise</h1>
        </div>
    </header>
    
    <nav>
        <ul>
            <li><a href="home.php">Accueil</a></li>
            <li><a href="post_internship.php">Publier une offre</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>

    <main class="container">
        <h2>Mes offres de stages</h2>
        <?php if (empty($internships)): ?>
            <p>Vous n'avez pas encore publié d'offres de stage.</p>
        <?php else: ?>
            <?php foreach ($internships as $internship): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($internship['titre']); ?></h3>
                    </div>
                    <div class="card-body">
                        <p><?php echo htmlspecialchars($internship['description']); ?></p>
                        <p>Date de début: <?php echo $internship['date_debut']; ?></p>
                        <p>Date de fin: <?php echo $internship['date_fin']; ?></p>
                    </div>
                    <div class="card-footer">
                        <a href="view_applications.php?offre_id=<?php echo $internship['id']; ?>" class="btn-primary">Voir les candidatures</a>
                        <a href="edit_internship.php?id=<?php echo $internship['id']; ?>" class="btn-warning">Modifier</a>
                        <form action="" method="post" style="display: inline;">
                            <input type="hidden" name="offre_id" value="<?php echo $internship['id']; ?>">
                            <button type="submit" name="delete_offre" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?');">Supprimer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>