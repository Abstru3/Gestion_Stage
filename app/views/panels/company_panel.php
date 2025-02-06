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

// Récupérer les offres de stage de l'entreprise
$stmt = $pdo->prepare("SELECT * FROM offres_stages WHERE entreprise_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$offres = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau Entreprise</title>
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style.css">
    <link rel="stylesheet" href="/Gestion_Stage/public/assets/css/style_company_panel.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Panneau Entreprise</h1>
        </div>
    </header>
    
    <nav>
        <ul>
<<<<<<< Updated upstream
            <li><a href="/Gestion_Stage/app/views/home.php">Mon espace</a></li>
            <li><a href="/Gestion_Stage/app/views/profile.php">Mon profil</a></li>
            <li><a href="/Gestion_Stage/app/views/auth/logout.php">Déconnexion</a></li>
=======
            <li><a href="/Gestion_Stage/app/views/home.php">Accueil</a></li>
            <li><a href="/Gestion_Stage/app/views/profile.php">📋 Mon profil</a></li>
            <li><a href="/Gestion_Stage/app/views/auth/logout.php">🚪 Déconnexion</a></li>
>>>>>>> Stashed changes
        </ul>
    </nav>

    <main class="container">
        <h2>Mes offres de stages</h2>
        <?php if (empty($offres)): ?>
            <p>Vous n'avez pas encore publié d'offres de stage.</p>
        <?php else: ?>
            <ul>
            <?php foreach ($offres as $offre): ?>
                <li class="company-offer">
                    <h3><?php echo htmlspecialchars($offre['titre']); ?></h3>
                    <p><?php echo htmlspecialchars($offre['description']); ?></p>
                    <p>Date de début: <?php echo $offre['date_debut']; ?></p>
                    <p>Date de fin: <?php echo $offre['date_fin']; ?></p>
                    <a class="modify-btn" href="/Gestion_Stage/app/views/internships/edit_internship.php?id=<?php echo $offre['id']; ?>">Modifier</a>
                    <a class="see-btn" href="/Gestion_Stage/app/views/internships/view_applications.php?offre_id=<?php echo $offre['id']; ?>">Voir les candidatures</a>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a href="/Gestion_Stage/app/views/internships/post_internship.php" class="btn btn-primary">Publier une nouvelle offre de stage</a>
    </main>

    <p><a class="index-button" href="/Gestion_Stage/index.php">Retour au menu</a></p>
</body>
</html>

